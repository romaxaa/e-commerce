<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../include/config.php';

session_start();

use Namshi\JOSE\SimpleJWS;

$inputJSON = file_get_contents('php://input');
$data = json_decode(file_get_contents('php://input'), true);

if (is_null($data)) 
{
    $data = $_POST;
}

$action = $data['type'] ?? '';

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
};