<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../search/productSearch.php';
require_once __DIR__ . '/../include/config.php';
require_once 'RabbitService.php';

session_start();

use Namshi\JOSE\SimpleJWS;
use Meilisearch\Client;
use App\Search\productsearch;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$client = new Client('http://meilisearch:7700', 'asdasd123');


$data = json_decode(file_get_contents('php://input'), true);

if (is_null($data)) 
{
    $data = $_POST;
}

$action = $data['type'] ?? '';

$rabbit = new RabbitService();

$secretKey = 'q123456789qRomix!';

function getuserid($secretKey)
{
    $token = $_COOKIE['token'] ?? null;

    if (!$token) 
    {
        return null; // Токена нет — пользователь не залогинен
    }

    try 
    {
        $secretKey = 'q123456789qRomix!';    
    
        $jws = SimpleJWS::load($token);

        if($jws->isValid($secretKey))
        {
            $payload = $jws->getPayload();

            if (isset($payload['exp']) && $payload['exp'] < time())
            {   
                return;
            }

            $user_id = (int)$payload['user_id'];

            return $user_id;
        } 
        else
        {
            return null;
        }
                 
    
        // 2. Расшифровываем токен
        //$decoded = JWT::decode($token, new Key($secret_key, 'HS256'));
        
        // 3. Возвращаем ID (то самое поле, которое ты указывал при создании)
        //return $decoded->user_id; 
    } 
    catch (Exception $e) 
    {
        // Токен просрочен или подделан
        return null; 
    }
}

function slugify($string) 
{
    $string = iconv("UTF-8", "ASCII//TRANSLIT//IGNORE",transliterator_transliterate("Any-Latin; NFD; [:Nonspacing Mark:] Remove; NFC; [:Punctuation:] Remove; Lower();", $string));//iconv нужен для ьъ
    $string = str_replace("?", "", $string);
    $string = preg_replace('/[-\s]+/', '-', $string);
    return trim($string, '-');
}

function UploadFile($folder, $module, $thumb = false, $watermark_file = null, $watermark = false) 
{

    $allow_extension_image = ['jpg','jpeg','png','webp','svg'];
    $allow_extension_audio = ['webm', 'ogg', 'mp3', 'wav', 'm4a'];

    /*if (!class_exists('Imagick')) 
    {
        return ['status' => false, 'error' => 'Imagick not installed'];
    }*/  

    if(!empty($_FILES))
    {
        $path = "/uploads/$module/$folder/";
        $uploadDir = $_SERVER['DOCUMENT_ROOT'].$path;
        @mkdir($uploadDir, 0755, true);
                
        $path_info = pathinfo($_FILES['file']['name']);     
        $ext = isset($path_info['extension']) ? strtolower($path_info['extension']) : '';

        // Если расширения нет (бывает при отправке Blobs), попробуем определить по MIME типу
        if (empty($ext)) 
        {
            $mime = $_FILES['file']['type'];
            if ($mime == 'audio/webm') $ext = 'webm';
            elseif ($mime == 'audio/ogg') $ext = 'ogg';
            elseif ($mime == 'audio/mpeg') $ext = 'mp3';
        }
        
        $name = uniqid().'_'.time();
        $is_audio = in_array($ext, $allow_extension_audio);
        $is_image = in_array($ext, $allow_extension_image);

        if(!$is_audio && !$is_image)
        {
            return ['status' => false, 'error' => 'Extension not valid: ' . $ext];
        }
        
        $fileName = $is_audio ? "{$name}.{$ext}" : "{$name}_thumb.jpg";
        
        $uploadFilePath = $uploadDir.$fileName;         

        if(move_uploaded_file($_FILES['file']['tmp_name'], $uploadFilePath))
        {
            if ($is_image) 
            {
                if (!class_exists('Imagick')) 
                {
                return ['status' => false, 'error' => 'Imagick not installed'];
                }

                $image = new Imagick($uploadFilePath);
                
                if ($thumb) 
                {
                $image->setImageFormat('jpg');
                $image->setImageCompressionQuality(70);
                $image->writeImage($uploadDir . $fileName);
                }
                
                if ($watermark) 
                {
                // Твоя функция наложения логотипа
                $image = logoImage($image, $watermark_file);  
                $image->writeImage($uploadFilePath);
                }
                $image->destroy();
            }

            // Для аудио ничего больше делать не нужно, оно уже сохранено
            return [
                'status' => true,
                'filename' => $path . $fileName
            ];
        }    
    }

    return [
        'status' => false,
        'error' => 'Error loading file'
    ];
}

switch ($action) 
{
    case 'login':

        $userId = getuserid($secretKey);

        if ($userId) 
        {
            http_response_code(401);
            echo json_encode(['error' => 'авторизован']);
            break;
        }

        if (isset($data['email']) && isset($data['password']))
        {
            $errors = array();      
            
            $user = R::findOne('users', 'email = ?', array($data['email']));
        
            if (empty($user))
            {
                $errors[] = json_encode(array('result' => 'error'));     
            }
            if(empty($errors))
            {
                if($user)
                {
                    if(password_verify($data['password'], $user->password))
                    {
                        $payload = [
                            'user_id' => $user->id,
                            'email' => $user->email,
                            'iat' => time(),
                            'exp' => time() + 3600
                        ];

                        $jws = new Namshi\JOSE\SimpleJWS(['alg' => 'HS256']);

                        //загружаем данные, и подписываем ключом.
                        $jws->setPayload($payload);
                        $jws->sign($secretKey);

                        //получаем готовую строчку токена
                        $token = $jws->getTokenString();

                        setcookie("token", $token, [
                            'expires' => time() + 3600,
                            'path' => '/',
                            'domain' => 'localhost',
                            'httponly' => true,         
                            'secure' => false,          
                            'samesite' => 'Lax',        
                        ]);

                        $_SESSION['logged_user'] = $user;

                        echo json_encode(array('result' => 'auth', 'token' => $token));
                        return;
                    }
                    else
                    {
                        echo json_encode(array('result' => 'error'));
                        return; 
                    }
                }
            }

        }
        echo array_shift($errors);return;   
    break 1;

    case 'register':
        $userId = getuserid($secretKey);

        if ($userId) 
        {
            http_response_code(401);
            echo json_encode(['error' => 'авторизован']);
            break;
        }

        if(isset($data['name']) && isset($data['email']) && isset($data['password']) && isset($data['password_repeat']))
        {
            $errors = array();

            if (R::count('users', "email = ?", array($data['email'])) > 0)
            {
                echo json_encode(['result' => 'matchemail']);
                exit;
            }
            if (R::count('users', "username = ?", array($data['name'])) > 0)
            {
                echo json_encode(['result' => 'matchusername']);
                exit;
            }
            if (iconv_strlen($data["password"]) < 2)
            {
                echo json_encode(['result' => 'minpass']);
                exit;
            }
            if ($data['password'] !== $data['password_repeat']) 
            {
                echo json_encode(['result' => 'passnotmuch']);
                exit;
            }
            if (empty($errors))
            {
                $bad = array('?', '!', ' ', '&', '*', '$', '#', '@', '+', '`', '"', "'", '=',',','/','<','>');
                $good = array('', '', '', '', '', '', '', '', '', '', '', '', '','','','','');
                $bademail = array('?', '!', ' ', '&', '*', '$', '#', '+', '`', '"', "'", '=',',','/','<','>');
                $goodemail = array('', '', '', '', '', '', '', '', '', '', '', '','','','',''); 
                 			
                $user = R::dispense('users');
                $user->username = htmlspecialchars(str_replace($bad, $good, $data['name']),ENT_QUOTES); 
                $user->email = htmlspecialchars(str_replace($bademail, $goodemail, $data['email']),ENT_QUOTES); 
                $user->emailverified = 0;
                $user->password = password_hash($data['password'], PASSWORD_DEFAULT); 
                $user->bio = null;
                $user->avatar ='images/cover_users.jpg';
                $user->group = 1;
                $user->cover = 'images/ava.png';
                $user->banned = '0';
                $user->lastjoin = date("Y-m-d H:i:s"); 
                R::store($user);

                $_SESSION['logged_user'] = $user;
                echo json_encode(array('result'=>'good'));
            } 
            else 
            {
                echo array_shift($errors);
                return;
            }
        }
    break 1;

    case 'get_profile':
        if(empty($_SESSION['logged_user']))
        {
            echo json_encode(array('result' => 'no_auth'));
            return;
        }

        $userinfo = R::load('users', $_SESSION['logged_user']->id);

        if(!empty($userinfo))
        {
            echo json_encode(array('result' => 'good', 'user' => $userinfo));
        }
    break 1;

    case 'get_products':

        $products = R::getall('SELECT p.*, c.name as category_name FROM products p INNER JOIN category c ON p.category_id = c.id');
        $brands = R::getall('SELECT * FROM brands');
            
        if($products)
        {
            foreach($products as $product)
            {
                if(isset($product['specifications']) && !empty($product['specifications'])) 
                {
                    $specs = json_decode($product['specifications'], true);
                    $product['specifications'] = $specs;

                    /*if(isset($specs['brand']) && !empty($specs['brand'])) 
                    {
                        $brandName = $specs['brand'];
                        
                        // Проверяем, есть ли уже такой бренд
                        $existingKey = null;
                        foreach($brands as $key => $brand) 
                        {
                            if($brand['name'] === $brandName) 
                            {
                                $existingKey = $key;
                                break;
                            }
                        }
                        
                        // Если бренда нет, добавляем с новым ID
                        if($existingKey === null) 
                        {
                            $brands[] = ['id' => count($brands) + 1, 'name' => $brandName];
                        }
                    }*/
                }  
            } 

            echo json_encode(['result' => 'good', 'data' => $products, 'count' => count($products), 'brands' => $brands]);
            return;
        }
        else
        {
            echo json_encode(['result' => 'bad']);
            return;
        }

    break 1;

    case 'get_product_by_slug':

        if(isset($data['slug']))
        {
            $product = R::getrow('SELECT p.*, c.name AS category_name FROM products p INNER JOIN category c ON p.category_id = c.id WHERE url = ?', [$data['slug']]);

            if($product)
            {
                $specifications = json_decode($product['specifications'], true);

                echo json_encode(['result' => 'good', 'data' => $product, 'specifications' => $specifications]);
                return;
            }
            else
            {
                echo json_encode(['result' => 'bad']);
                return;
            }
        }
        else
        {
            echo json_encode(['result' => 'no_slug']);
            return;
        }

    break 1;

    case 'create-product':
        if(!isset($data['name']) && !isset($data['category']) && !isset($data['price']) && !isset($data['stock']) && !isset($data['description']) && !isset($data['image']))
        {
            echo json_decode(['result' => 'no_data']);
            return;
        }
        
        if(empty($_SESSION['logged_user']))
        {
            echo json_encode(['result' => 'auth']);
            return;
        }   

        if($_SESSION['logged_user']->group != 99)
        {
            echo json_encode(['result' => 'group']);
            return;
        }

        $bad = array('?', '!', ' ', '&', '*', '$', '#', '@', '+', '`', '"', "'", '=',',','/','<','>');
        $good = array('', '', '-', '', '', '', '', '', '', '', '', '', '','','','','');    

        if(preg_match("/[А-Яа-я]/", $data['name'])) 
        {
            $url = slugify($data['name']);
            $url = str_replace($bad, $good, $url);
            $url = htmlspecialchars($url,ENT_QUOTES);
        }
        else
        {
            $url = str_replace($bad, $good, $data['name']);
            $url = htmlspecialchars($url,ENT_QUOTES);
        }

        try 
        {
            $products = R::dispense('products');
            $products->name = htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8');
            $products->subtitle = htmlspecialchars($data['description'], ENT_QUOTES, 'UTF-8');
            $products->category_id = htmlspecialchars($data['category'], ENT_QUOTES, 'UTF-8');
            $products->specifications = null;
            $products->img = htmlspecialchars($data['image'], ENT_QUOTES, 'UTF-8');
            $products->url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
            $products->price = htmlspecialchars($data['price'], ENT_QUOTES, 'UTF-8');
            $products->oldprice = htmlspecialchars($data['oldPrice'], ENT_QUOTES, 'UTF-8');
            $products->stock = htmlspecialchars($data['stock'], ENT_QUOTES, 'UTF-8');
            $products->isNew = htmlspecialchars($data['isNew'], ENT_QUOTES, 'UTF-8');
            $products->isPopular = htmlspecialchars($data['isPopular'], ENT_QUOTES, 'UTF-8');
            $id = R::store($products);

            echo json_encode(['result' => 'good']);
            return;
            
        } catch (Exception $e) 
        {
            echo json_encode(['result' => 'error', 'message' => 'Ошибка базы данных: ' . $e->getMessage()]);
            return;
        }
    break 1;

    case 'update-product':
        if(!isset($data['id']))
        {
            echo json_encode(['result' => 'no_id']);
            return;
        }
        
        if(empty($_SESSION['logged_user']))
        {
            echo json_encode(['result' => 'auth']);
            return;
        }   

        if($_SESSION['logged_user']->group != 99)
        {
            echo json_encode(['result' => 'group']);
            return;
        }

        try 
        {
            $product = R::load('products', $data['id']);

            if (!$product->id) {
                echo json_encode(['result' => 'error', 'message' => 'Товар с таким ID не найден']);
                return;
            }

            // Обновляем поля, только если они переданы
            if (isset($data['name'])) 
            {
                $product->name = htmlspecialchars($data['name'], ENT_QUOTES);
                
                // Логика формирования URL (слага)
                $bad = array('?', '!', ' ', '&', '*', '$', '#', '@', '+', '`', '"', "'", '=', ',', '/', '<', '>');
                $good = array('', '', '-', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
                
                $url = slugify($data['name']); // Убедись, что функция slugify доступна
                $url = str_replace($bad, $good, $url);
                $product->url = htmlspecialchars($url, ENT_QUOTES);
            }

            if (isset($data['description'])) $product->subtitle = htmlspecialchars($data['description'], ENT_QUOTES);
            if (isset($data['category']))    $product->category_id = $data['category'];
            if (isset($data['image']))       $product->img = htmlspecialchars($data['image'], ENT_QUOTES);
            if (isset($data['price']))       $product->price = $data['price'];
            if (isset($data['oldPrice']))    $product->oldprice = $data['oldPrice'];
            if (isset($data['stock']))       $product->stock = (int)$data['stock'];
            
            // Важно для чекбоксов: приводим к 1 или 0
            if (isset($data['isNew']))       $product->is_new = $data['isNew'] ? 1 : 0;
            if (isset($data['isPopular']))   $product->is_popular = $data['isPopular'] ? 1 : 0;

            // Сохраняем объект
            R::store($product);

            echo json_encode(['result' => 'good']);
            return;

        } catch (Exception $e) {
            echo json_encode(['result' => 'error', 'message' => $e->getMessage()]);
            return;
        }
    break 1;

    case 'delete-product':
        if(isset($data['id']))
        {
            if(empty($_SESSION['logged_user']))
            {
                echo json_encode(['result' => 'auth']);
                return;
            }

            if($_SESSION['logged_user']->group != 99)
            {
                echo json_encode(['result' => 'group']);
                return;
            }

            try 
            {
                R::exec('DELETE FROM `products` WHERE id = ?', [$data['id']]);
                echo json_encode(['result' => 'good']);
                return;
                
            } catch (Exception $e) 
            {
                echo json_encode(['result' => 'error', 'message' => 'Ошибка базы данных: ' . $e->getMessage()]);
                return;
            }
        }
    break 1;

    case 'get_categories':

        $categories = R::getall('SELECT * FROM category');

        echo json_encode(['result' => 'good', 'categories' => $categories]);
        return;

        if(empty($categories))
        {
            echo json_encode(['result' => 'emty']);
            return;
        }
    break 1;

    case 'create-category':

        if(!isset($data['category_name']) || !isset($data['icon']))
        {
            echo json_encode(['result' => 'error']);
            return;
        }
        
        if(empty($_SESSION['logged_user']))
        {
            echo json_encode(['result' => 'auth']);
            return;
        }
        
        if($_SESSION['logged_user']->group != 99)
        {
            echo json_encode(['result' => 'group']);
            return;
        }
        
        if(iconv_strlen($data['category_name']) < 1)
        {
            echo json_encode(['result' => 'min']);
            return;
        }

        try 
        {
            $category = R::dispense('category');
            $category->name = htmlspecialchars($data['category_name'], ENT_QUOTES, 'UTF-8');
            $category->icon = htmlspecialchars($data['icon'], ENT_QUOTES, 'UTF-8');
            $id = R::store($category);
            
            echo json_encode(['result' => 'good']);
            return;
            
        } catch (Exception $e) 
        {
            echo json_encode(['result' => 'error', 'message' => 'Ошибка базы данных: ' . $e->getMessage()]);
            return;
        }
    
    break 1;

    case 'update-category':
        if(!isset($data['id']) || !isset($data['name']))
        {
            echo json_encode(['result' => 'no_data']);
            return;
        }

        if(empty($_SESSION['logged_user']))
        {
            echo json_encode(['result' => 'auth']);
            return;
        }
        
        if($_SESSION['logged_user']->group != 99)
        {
            echo json_encode(['result' => 'group']);
            return;
        }
        
        if(iconv_strlen($data['name']) < 1)
        {
            echo json_encode(['result' => 'min']);
            return;
        }

        try 
        {
            $updated = false;

            if(isset($data['name']))
            {
                R::exec('UPDATE category SET name = ? WHERE id = ?', [htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8'), $data['id']]);
                $updated = true;
            }
            if(isset($data['icon']))
            {
                R::exec('UPDATE category SET icon = ? WHERE id = ?', [htmlspecialchars($data['icon'], ENT_QUOTES, 'UTF-8'), $data['id']]);
                $updated = true;
            }

            if($updated)
            {
                echo json_encode(['result' => 'good']);
                return;
            }
            else
            {
                echo json_encode(['result' => 'nothing']);
                return;
            }
            
        } catch (Exception $e) 
        {
            echo json_encode(['result' => 'error', 'message' => 'Ошибка базы данных: ' . $e->getMessage()]);
            return;
        }
    break 1;

    case 'delete-category':
        if(isset($data['id']))
        {
            $categories = R::find('products', 'category_id = ?', [$data['id']]);

            if(empty($_SESSION['logged_user']))
            {
                echo json_encode(['result' => 'auth']);
                return;
            }
            
            if($_SESSION['logged_user']->group != 99)
            {
                echo json_encode(['result' => 'group']);
                return;
            }

            if($categories)
            {
                echo json_encode(['result' => 'have_category']);
                return;
            }

            try 
            {
                R::exec('DELETE FROM category WHERE id = ?', [$data['id']]);    
                echo json_encode(['result' => 'good']);
                return;
                
            } catch (Exception $e) 
            {
                echo json_encode(['result' => 'error', 'message' => 'Ошибка базы данных: ' . $e->getMessage()]);
                return;
            }
        }
    break 1;

    case 'update-user-info':

        if(empty($_SESSION['logged_user']))
        {
            echo json_encode(['result' => 'auth']);
            return;
        }

        $currentUser = R::load('users', $data['id']);



        //if(isset($data['id']) && isset($data['name']) && isset($data['surname']) && isset($data['email']) && isset($data['phone']) && isset($data['birthday']) && isset($data['bio']))
        if(isset($data['id']))
        {
            if(!empty($data['username']))
            {
                if(R::count('users', "username = ? AND id != ?", [$data['username'], $currentUser->id]) > 0)
                {
                    echo json_encode(['result' => 'username']);
                    return;
                }
            }
            if(!empty($data['email']))
            {
                if(R::count('users', "email = ? AND id != ?", [$data['email'], $currentUser->id]) > 0)
                {
                    echo json_encode(['result' => 'email']);
                    return;
                }
            }

            if(!empty($data['name']) && !empty($data['surname']))
            {
                if(iconv_strlen($data['name']) < 2 || iconv_strlen($data['surname']) < 2)
                {
                    echo json_encode(['result' => 'minnames']);
                    return;
                }
            }

            $updated = false;

            if(!empty($data['username']))
            {
                R::exec('UPDATE users SET username = ? WHERE id = ?', [
                    htmlspecialchars($data['username'], ENT_QUOTES), 
                    $data['id']
                ]);
                $updated = true;
            }

            if(!empty($data['name']))
            {
                R::exec('UPDATE users SET name = ? WHERE id = ?', [
                    htmlspecialchars($data['name'], ENT_QUOTES), 
                    $data['id']
                ]);
                $updated = true;
            }

            if(!empty($data['surname']))
            {
                R::exec('UPDATE users SET surname = ? WHERE id = ?', [
                    htmlspecialchars($data['surname'], ENT_QUOTES), 
                    $data['id']
                ]);
                $updated = true;
            }

            if(!empty($data['email']))
            {
                R::exec('UPDATE users SET email = ? WHERE id = ?', [
                    htmlspecialchars($data['email'], ENT_QUOTES), 
                    $data['id']
                ]);
                $updated = true;
            }

            if(!empty($data['phone']))
            {
                R::exec('UPDATE users SET phone = ? WHERE id = ?', [
                    htmlspecialchars($data['phone'], ENT_QUOTES), 
                    $data['id']
                ]);
                $updated = true;
            }

            if(!empty($data['birthday']))
            {
                R::exec('UPDATE users SET birthday = ? WHERE id = ?', [
                    htmlspecialchars($data['birthday'], ENT_QUOTES), 
                    $data['id']
                ]);
                $updated = true;
            }

            if(!empty($data['bio']))
            {
                R::exec('UPDATE users SET bio = ? WHERE id = ?', [
                    htmlspecialchars($data['bio'], ENT_QUOTES), 
                    $data['id']
                ]);
                $updated = true;
            }

            if(!empty($data['notifications']))
            {
                R::exec('UPDATE users SET notifications = ? WHERE id = ?', [
                    htmlspecialchars($data['notifications'], ENT_QUOTES), 
                    $data['id']
                ]);
                $updated = true;
            }

            if(!empty($data['visible']))
            {
                R::exec('UPDATE users SET visible = ? WHERE id = ?', [
                    htmlspecialchars($data['visible'], ENT_QUOTES), 
                    $data['id']
                ]);
                $updated = true;
            }

            if($updated == true)
            {
                echo json_encode(['result' => 'good']);
                return;
            }
            else
            {
                echo json_encode(['result' => 'not-update']);
                return;
            }
        }
    break 1;

    case 'add-to-cart':
        if(empty($_SESSION['logged_user']))
        {
            echo json_encode(['result' => 'auth']);
            return;
        }

        if(empty($data['count']))
        {
            $count = 1;
        }
        else
        {
            $count = $data['count'];
        }

        $cart = R::dispense('cart');
        $cart->user_id = $_SESSION['logged_user']->id;
        $cart->product_id = htmlspecialchars((int)$data['id']);
        $cart->count = $count;
        $id = R::store($cart);

        echo json_encode(['result' => 'good']);
        return;
    break 1;

    case 'fetch-cart':
        $cart = R::getall('SELECT c.user_id, c.count, c.product_id, p.price as product_price, p.name as product_name, p.stock as product_stock, p.img, cat.name as category_name FROM cart c INNER JOIN products p ON c.product_id = p.id INNER JOIN category cat ON p.category_id = cat.id');

        if(!empty($cart))
        {
            echo json_encode(['result' => 'good', 'products' => $cart]);
            return;
        }
        else
        {
            echo json_encode(['result' => 'empty']);
            return;
        }
    break 1;

    case 'apply-promocode':
        if(!isset($data['promocode']))
        {
            echo json_encode(['result' => 'no_data']);
            return;
        }

        $promocodes = R::getrow('SELECT discount FROM promocodes WHERE promocode = ?', [$data['promocode']]);
    break 1;

    case 'checkout':
        $data['user_id'] = $_SESSION['logged_user']->id;
        $rabbit->publish('order.created', $data);

        echo json_encode(['result' => 'good']);

        /*if(empty($data['adress_id']))
        {
            echo json_encode(['result' => 'empty_adress']);
            return;
        }

        $checkCart = R::getAll('SELECT c.user_id, c.product_id, c.count as product_count, p.price FROM cart c INNER JOIN products p ON c.product_id = p.id WHERE c.user_id = ?', [$_SESSION['logged_user']->id]);

        if(!empty($checkCart))
        {
            $count = 0;
            $totalSum = 0;
            foreach($checkCart as $item)
            {
                $count += $item['product_count'];
                $totalSum += $item['price'] * $count;
            }
        }
        else
        {
            echo json_encode(['result' => 'empty_cart']);
            return;
        }

        if(isset($checkCart))
        {
            $order = R::dispense('orders');
            $order->user_id = htmlspecialchars((int)$_SESSION['logged_user']->id);
            $order->payment_type = htmlspecialchars($data['payment'], ENT_QUOTES);

            $order->total_price = htmlspecialchars((int)$totalSum);
            $order->status = 'paid';
            $order->delivery_type = htmlspecialchars($data['delivery'], ENT_QUOTES);
            $order->adress_id = htmlspecialchars($data['adress_id'], ENT_QUOTES);
            $order->created_at = date("Y-m-d H:i:s");
            $id = R::store($order);

            if(!empty($id))
            {
                $orderitem = R::dispense('orderitem');
                $orderitem->order_id = htmlspecialchars((int)$id);
                foreach($checkCart as $row)
                {
                    $orderitem->product_id = $row['product_id'];
                }
                $orderitem->quantity = $count;
                $orderitem->price = $totalSum;
                $orderitem->config = null;
                $id_items = R::store($orderitem);

                if(!empty($id_items))
                {
                    R::exec('DELETE FROM `cart` WHERE `user_id` = ?', [$_SESSION['logged_user']->id]);
                    echo json_encode(['result' => 'good']);
                    return;
                }

            }
        }*/
    break 1;

    case 'remove-cart-product':
        if(isset($data['id']))
        {
            $delete = R::exec('DELETE FROM `cart` WHERE product_id = ?', [$data['id']]);

            if($delete)
            {
                echo json_encode(['result' => 'good']);
                return;
            }
        }
        else
        {
            echo json_encode(['result' => 'no_data', 'message' => 'No data']);
            return;
        }
    break 1;

    case 'fetch-users':
        $users = R::getall('SELECT * FROM users');

        if($users)
        {
            echo json_encode(['result' => 'good', 'data' => $users]);
            return;
        }
        else
        {
            echo json_encode(['result' => 'bad']);
            return; 
        }
    break 1;

    case 'delete-user':
        if(empty($_SESSION['logged_user']) || $_SESSION['logged_user']->group != 99)
        {
            echo json_encode(['result' => 'auth']);
            return;
        }
        if($data['id'] == $_SESSION['logged_user']->id)
        {
            echo json_encode(['result'=> 'id_much']);
            return;
        }
        
        $update = R::exec('DELETE FROM `users` WHERE id = ?', [$data['id']]);
        
        if($update)
        {
            echo json_encode(['result' => 'good']);
            return;
        }
        else
        {
            echo json_encode(['result' => 'bad']);
            return;
        }
    break 1;

    case 'search-product':
        if(!isset($data['query']) && !isset($data['categories']))
        {
            echo json_encode(['result' => 'no_query']);
            return;
        }
        else
        {
            $search = new ProductSearch($client);
            $filters = [];

            if (!empty($data['categories'])) {
                // Оборачиваем каждое название в одинарные кавычки
                $formattedCats = array_map(function($cat) {
                    return "'" . $cat . "'";
                }, $data['categories']);
                
                $catList = implode(', ', $formattedCats);
                $filters[] = "category IN [$catList]";
            }

            if (!empty($data['brands'])) {
                $formattedBrands = array_map(function($b) {
                    return "'" . $b . "'";
                }, $data['brands']);
                
                $brandList = implode(', ', $formattedBrands);
                $filters[] = "brand IN [$brandList]";
            }

            $filterString = implode(' AND ', $filters);

            $index = $client->index('products');
            $result = $index->search($data['query'], ['filter' => $filterString]);
            $products = $result->getHits();

            echo json_encode(['result' => 'good', 'data' => $products]);
            return;
        }
    break 1;

    case 'fetch-cities':
        if(empty($_SESSION['logged_user']))
        {
            echo json_encode(['result' => 'auth']);
            return;
        }
        //SELECT gc.name as city_name, gr.name as region_name FROM user_adresses ua INNER JOIN geo_city gc ON ua.city = gc.id INNER JOIN geo_regions gr ON ua.region = gr.id WHERE ua.user_id = 1;
        $adresses = R::getAll('SELECT ua.*,  user_adresses ua INNER JOIN ');
    break 1;

    case 'save-address':
        echo json_decode(['result' => 'bad', 'message' => 'нет данных']);
        return;
        /*if(empty($_SESSION['logged_user']))
        {
            echo json_encode(['result' => 'auth']);
            return;
        }

        if(!isset($data['id']))
        {
            echo json_encode(['result' => 'no_id']);
            return;
        }

        if(isset($data['id']) && isset($data['type']) && isset($data['street']) && isset($data['city']) && isset($data['postalCode']) && isset($data['office']) && isset($data['isDefault']))
        {
            $region = R::getrow('SELECT region_id FROM geo_city WHERE id = ?', [$data['city']]);

            if(!$region)
            {
                echo json_encode(['result' => 'no_region']);
                return;
            }

            $regionId = $region['region_id'];

            $adress = R::dispense('user_adresses');
            $adress->user_id = htmlspecialchars($data['id'], ENT_QUOTES);
            $adress->region = htmlspecialchars($regionId, ENT_QUOTES);
            $adress->city = htmlspecialchars($data['city'], ENT_QUOTES);
            $adress->city_index = htmlspecialchars($data['postalCode'], ENT_QUOTES);
            $adress->street = htmlspecialchars($data['street'], ENT_QUOTES);
            $adress->office = htmlspecialchars($data['office'], ENT_QUOTES);
            $adress->status = htmlspecialchars($data['type'], ENT_QUOTES);
            $id = R::store($adress);

            if($adress)
            {
                echo json_encode(['result' => 'good']);
                return;
            }
            else
            {
                echo json_encode(['result' => 'bad']);
                return;
            }
        }
        else
        {
            echo json_encode(['result' => 'bad', 'message' => 'данные не приходят']);
            return;
        }*/
    break 1;

    case 'edit-pass':
        if(empty($_SESSION['logged_user']))
        {
            echo json_encode(['result' => 'auth']);
            return;
        }

        if(isset($data['current']) && isset($data['new_pass']) && isset($data['confirm']))
        {
            if($data['new_pass'] != $data['confirm'])
            {
                echo json_encode(['result' => 'confirm_pass_not_much']);
                return;
            }

            $pass = R::getCell('SELECT password FROM users WHERE id = ?', [$_SESSION['logged_user']->id]);

            if($pass)
            {
                if(password_verify($data['current'], $pass))
                {
                    $hash = password_hash($data['new_pass'], PASSWORD_DEFAULT);
                    R::exec('UPDATE users SET password = ? WHERE id = ?', [$hash, $_SESSION['logged_user']->id]);

                    echo json_encode(['result' => 'good']);
                    return;
                }
                else
                {
                    echo json_encode(['result' => 'pass_not_much']);
                    return;
                }
            }
        }
        else
        {
            echo json_encode(['result' => 'bad', 'message' => 'no data']);
            return;
        }
    break 1;

    case 'fetch-comments':
        if(!isset($data['id']))
        {
            echo json_encode(['result' => 'empty_id']);
            return;
        }

        try
        {
            $comments = R::getall('SELECT r.*, u.name, u.surname, u.avatar FROM reviews r INNER JOIN users u ON r.user_id = u.id WHERE r.product_id = ?', [$data['id']]);

            if($comments)
            {
                $count = R::getCell('SELECT COUNT(product_id) FROM `reviews` WHERE `product_id` = 1');
                if($count)
                {
                    echo json_encode(['result' => 'good', 'comments' => $comments, 'count' => $count]);
                    return;
                }
                else
                {
                    echo json_encode(['result' => 'good', 'comments' => $comments]);
                    return;
                }
            }
            else
            {
                echo json_encode(['result' => 'no_comments']);
                return;
            }
        }
        catch(Exception $e)
        {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    break 1;

    case 'fetch_cityes':
        if(empty($_SESSION['logged_user']))
        {
            echo json_encode(['result' => 'auth']);
            return;
        }
        
        $cities = R::getall('SELECT * FROM geo_city');
        $regions = R::getall('SELECT * FROM geo_regions');

        if($cities && $regions)
        {
            echo json_encode(['result' => 'good', 'cities' => $cities, 'regions' => $regions]);
            return;
        }
        else
        {
            echo json_encode(['result' => 'bad']);
            return;
        }
        
    break 1;

    case 'create-comment':
        if(!isset($data['product_id']))
        {
            echo json_encode(['result' => 'no_id']);
            return;
        }
        if(!isset($data['rating']))
        {
            echo json_encode(['result' => 'no_rating']);
            return;
        }
        if(empty($_SESSION['logged_user']))
        {
            echo json_encode(['result' => 'auth']);
            return;
        }

        $comment = R::dispense('reviews');
        $comment->product_id = htmlspecialchars($data['product_id'], ENT_QUOTES);
        $comment->user_id = $_SESSION['logged_user']->id;
        if(isset($data['content']))
        {
            $comment->comment = htmlspecialchars($data['content'], ENT_QUOTES);
        }
        $comment->grade = htmlspecialchars($data['rating'], ENT_QUOTES);
        $comment->created_at = date("Y-m-d H:i:s");
        $id = R::store($comment);

        if($comment)
        {
            echo json_encode(['result' => 'good']);
            return;
        }
        else
        {
            echo json_encode(['result' => 'bad']);
            return;
        }

    break 1;

    case 'create-pdf':
        $mail = new PHPMailer(true);
        $html = '<!DOCTYPE html>
            <html lang="ru">
            <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>GlassShop — Подтверждение заказа</title>
            <style>
                /* Базовые стили для email-клиентов */
                * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                }
                body {
                background-color: #e9eef3;
                line-height: 1.5;
                padding: 20px;
                }
                .email-wrapper {
                max-width: 600px;
                margin: 0 auto;
                background: #ffffff;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 8px 20px rgba(0,0,0,0.05);
                }
                .email-header {
                background: #0a0a0f;
                padding: 24px 30px;
                text-align: center;
                border-bottom: 3px solid #c084fc;
                }
                .email-header h1 {
                color: #ffffff;
                font-size: 26px;
                font-weight: 700;
                letter-spacing: -0.3px;
                margin: 0 0 6px;
                }
                .email-header p {
                color: #a1a1aa;
                font-size: 14px;
                margin: 0;
                }
                .email-body {
                padding: 32px 30px;
                }
                .greeting {
                font-size: 18px;
                font-weight: 600;
                color: #18181b;
                margin-bottom: 8px;
                }
                .order-id {
                background: #f4f4f6;
                display: inline-block;
                padding: 6px 14px;
                border-radius: 40px;
                font-size: 13px;
                font-weight: 500;
                color: #27272a;
                margin: 16px 0 12px;
                }
                .order-status {
                background: #e6f7ec;
                color: #1e7b3c;
                font-size: 13px;
                font-weight: 600;
                padding: 6px 12px;
                border-radius: 40px;
                display: inline-block;
                margin-left: 10px;
                }
                .info-grid {
                background: #f8fafc;
                border-radius: 16px;
                padding: 18px 20px;
                margin: 24px 0;
                border: 1px solid #e2e8f0;
                }
                .info-row {
                display: flex;
                justify-content: space-between;
                margin-bottom: 12px;
                font-size: 14px;
                }
                .info-row:last-child {
                margin-bottom: 0;
                }
                .info-label {
                color: #4b5563;
                font-weight: 500;
                }
                .info-value {
                color: #1f2937;
                font-weight: 500;
                }
                table {
                width: 100%;
                border-collapse: collapse;
                margin: 24px 0 20px;
                }
                th {
                text-align: left;
                padding: 12px 6px 8px 0;
                font-size: 13px;
                color: #6c757d;
                border-bottom: 1px solid #e9ecef;
                font-weight: 600;
                }
                td {
                padding: 12px 6px 12px 0;
                border-bottom: 1px solid #f0f2f5;
                font-size: 14px;
                color: #212529;
                }
                .total-line {
                display: flex;
                justify-content: space-between;
                padding: 10px 0;
                font-size: 15px;
                border-top: 1px solid #e2e8f0;
                margin-top: 12px;
                }
                .grand-total {
                font-size: 20px;
                font-weight: 800;
                color: #0a0a0f;
                border-top: 2px solid #cbd5e1;
                padding-top: 16px;
                margin-top: 8px;
                }
                .warranty-block {
                background: #fefce8;
                border-left: 4px solid #eab308;
                padding: 16px 20px;
                border-radius: 14px;
                margin: 28px 0 24px;
                }
                .warranty-block p {
                margin: 5px 0;
                font-size: 13px;
                color: #854d0e;
                }
                .button {
                display: inline-block;
                background-color: #0a0a0f;
                color: #ffffff;
                text-decoration: none;
                padding: 12px 28px;
                border-radius: 40px;
                font-weight: 600;
                font-size: 15px;
                margin: 10px 0 8px;
                }
                .footer {
                background-color: #f9fafb;
                padding: 24px 30px;
                text-align: center;
                border-top: 1px solid #eef2f6;
                font-size: 12px;
                color: #6c757d;
                }
                .footer a {
                color: #3b82f6;
                text-decoration: none;
                }
                @media (max-width: 500px) {
                .email-body {
                    padding: 24px 20px;
                }
                .info-row {
                    flex-direction: column;
                    gap: 4px;
                }
                td, th {
                    font-size: 12px;
                }
                }
            </style>
            </head>
            <body>
            <div class="email-wrapper">
                <!-- Шапка письма -->
                <div class="email-header">
                <h1>GlassShop</h1>
                <p>Премиальная электроника</p>
                </div>

                <!-- Основной контент -->
                <div class="email-body">
                <div class="greeting">Здравствуйте, Алексей!</div>
                <p style="margin: 8px 0 0px; color: #3b3b45;">Спасибо за покупку! Ваш заказ успешно оплачен и поступил в обработку.</p>
                
                <div>
                    <span class="order-id">Заказ № GL-2405-892</span>
                    <span class="order-status">✅ ОПЛАЧЕН</span>
                </div>

                <!-- Детали -->
                <div class="info-grid">
                    <div class="info-row">
                    <span class="info-label">Дата заказа:</span>
                    <span class="info-value">14 мая 2025, 14:32 МСК</span>
                    </div>
                    <div class="info-row">
                    <span class="info-label">Способ оплаты:</span>
                    <span class="info-value">Банковская карта (Visa) •• 4678</span>
                    </div>
                    <div class="info-row">
                    <span class="info-label">Доставка:</span>
                    <span class="info-value">Курьерская, Бесплатно</span>
                    </div>
                    <div class="info-row">
                    <span class="info-label">Адрес получения:</span>
                    <span class="info-value">г. Москва, ул. Тверская, д. 15, кв. 45</span>
                    </div>
                </div>

                <!-- Список товаров -->
                <h3 style="font-size: 18px; margin-bottom: 8px;">🛍️ Состав заказа</h3>
                <table>
                    <thead>
                    <tr><th>Товар</th><th>Кол-во</th><th>Цена</th></tr>
                    </thead>
                    <tbody>
                    <tr><td>iPhone 15 Pro 256GB (Титан)</td><td>1 шт</td><td>89 990 ₽</td></tr>
                    <tr><td>Sony WH-1000XM5 (черный)</td><td>1 шт</td><td>24 990 ₽</td></tr>
                    <tr><td>Apple Watch Series 9 (45mm)</td><td>2 шт</td><td>71 980 ₽</td></tr>
                    </tbody>
                </table>
                
                <!-- Итоговая сумма -->
                <div class="total-line">
                    <span>Стоимость товаров:</span>
                    <span>186 960 ₽</span>
                </div>
                <div class="total-line">
                    <span>Скидка (WELCOME10):</span>
                    <span>- 18 696 ₽</span>
                </div>
                <div class="total-line">
                    <span>Доставка:</span>
                    <span>0 ₽</span>
                </div>
                <div class="grand-total">
                    Итого к оплате: 168 264 ₽
                </div>

                <!-- Блок гарантии -->
                <div class="warranty-block">
                    <p>🛡️ <strong>Гарантия 12 месяцев</strong> — официальная гарантия на всю технику.</p>
                    <p>🔄 Если товар не подошел — вы можете вернуть его в течение 14 дней.</p>
                    <p>🔧 Сервисная поддержка доступна по телефону 8 (800) 555-35-35.</p>
                </div>

                <!-- Кнопка отслеживания -->
                <div style="text-align: center;">
                    <a href="#" class="button">📦 Отследить заказ</a>
                    <p style="font-size: 12px; color: #7f8c8d; margin-top: 12px;">Статус заказа также доступен в личном кабинете.</p>
                </div>
                </div>

                <!-- Footer -->
                <div class="footer">
                <p>© 2025 GlassShop — Техника, которой доверяют.<br>
                <a href="#">Политика конфиденциальности</a> | <a href="#">Помощь</a></p>
                <p style="margin-top: 12px;">Это письмо было отправлено автоматически, пожалуйста, не отвечайте на него.<br>
                Если у вас есть вопросы, свяжитесь с поддержкой: support@glassshop.ru</p>
                </div>
            </div>
            </body>
            </html>
        ';

        try 
        {
            $mail->isSMTP();

            $mail->Host = 'mailpit';
            $mail->Port = 1025;

            $mail->SMTPAuth = false;

            $mail->CharSet = 'UTF-8';

            $mail->setFrom('shop@test.local', 'GlassShop');

            $mail->addAddress('client@test.local');

            $mail->isHTML(true);

            $mail->Subject = 'Детали заказа №123123';

            $mail->Body = $html;

            $mail->send();

            echo json_encode(['result' => 'good']);
            return;

        } catch (Exception $e) 
        {
            echo "Ошибка: {$mail->ErrorInfo}";
        }
        /*$options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        $pdf = $dompdf->output();

        if($pdf)
        {
            file_put_contents('new-file.pdf', $pdf);
            
            echo json_encode(['result' => 'good']);
            return;
        }*/
    break 1;

    case 'logout':
        if(isset($_SESSION['logged_user']))
        {
            unset($_SESSION["logged_user"]);

            echo json_encode(array('result' => 'good'));
            return;
        }   
    break 1;
};