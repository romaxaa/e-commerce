import { defineStore } from 'pinia';
import axios from 'axios';
import { useRouter } from 'vue-router';
import Profile from '../components/profile.vue';

const router = useRouter();

export const useAuthStore = defineStore('auth', {
  state: () => ({
    //переменные пользователя
    user: null, // Изначально данных нет
    isLoaded: false,
    loading: false,
    error: null,

    //переменные категорий
    categories: null
  }),

  getters: 
  {
    isAuthenticated: (state) => {
      return !!state.user && state.user !== null;
    },
    userName: (state) => state.user?.name || '',
    userEmail: (state) => state.user?.email || '',
    userRole: (state) => state.user?.role || 'guest',
    userId: (state) => state.user?.id || null
  },

  //Pinia что-то типо отдельного файла jquery где мы отправляем запросы на бек. Только тут мы делаем условно шаблон, здесь находиться весь основной код. А на фронте просто вызывает функцию.
  actions: 
  {
    // Метод для установки данных (после успешного логина)
    setUser(userData) 
    {
      this.user = userData;
      
      // Сохраняем в localStorage для персистентности
      if (userData) 
      {
        localStorage.setItem('user', JSON.stringify(userData));
      } else 
      {
        localStorage.removeItem('user');
      }
    },

    async login(email, password) 
    {
      this.loading = true;
      this.error = null;
      
      try 
      {
        const response = await axios.post('/api/json.php', {
          type: 'login',
          email: email,
          password: password
        });
        
        if (response.data.result === 'auth') 
        {
          // Устанавливаем пользователя
          this.setUser(response.data.user);
          
          // Обновляем заголовки axios для будущих запросов
          if (response.data.token) 
          {
            axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
          }
        
          return { success: true, user: this.user };
        } 
        else 
        {
          this.error = response.data.message || 'Ошибка входа';
          return { success: false, error: this.error };
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Ошибка соединения';
        return { success: false, error: this.error };
      } finally {
        this.loading = false;
      }
    },

    async register(name, email, password, password_repeat) 
    {
      this.loading = true;
      this.error = null;
      
      try 
      {
        const response = await axios.post('/api/json.php', {
          type: 'register',
          name: name,
          email: email,
          password: password,
          password_repeat: password_repeat
        });
        
        if (response.data.result === 'good') 
        {
          // Автоматически входим после регистрации
          return await this.login(userData.email, userData.password);
        } 
        else 
        {
          this.error = response.data.message || 'Ошибка регистрации';
          return { success: false, error: this.error };
        }
      } 
      catch (error) 
      {
        this.error = error.response?.data?.message || 'Ошибка соединения';
        return { success: false, error: this.error };
      } finally {
        this.loading = false;
      }
    },

    //работа с категориями
    async fetchCategories()
    {
      try
      {
        const response = await axios.post('/api/json.php', {type: 'get_categories'});

        if(response.data.result === 'good')
        {
          this.categories = response.data.categories;
          return { success: true, categories: this.categories };
        }
      }
      catch(error)
      {
        return { success: false, error: this.error };
      }
      finally
      {
        this.isLoaded = true;
      }
    },

    async createCategory(name, icon)
    {
      try
      {
        const response = await axios.post('/api/json.php', {
          type: 'create-category',
          category_name: name,
          icon: icon
        }); 

        if(response.data.result === 'good')
        {
          //console.log('good!');
          return { success: true, data: response.data };
        }
        else 
        {
          this.error = response.data.message;
          return { success: false, error: this.error };
        }
      }
      catch (error) 
      {
        this.error = error.response?.data?.message || 'Ошибка соединения';
        return { success: false, error: this.error };
      } 
       finally 
      {
        this.loading = false;
      }
    },

    async updateCategory(id, name, icon)
    {
      try
      {
        const response = await axios.post('/api/json.php', {
        type: 'update-category',
        id: id,
        name: name,
        icon: icon
        });

        if(response.data.result === 'good')
        {
          console.log('good!');
          return { success: true, data: response.data };
        }
        else 
        {
          this.error = response.data.message;
          return { success: false, error: this.error };
        }
      }
      catch (error) 
      {
        this.error = error.response?.data?.message || 'Ошибка соединения';
        return { success: false, error: this.error };
      } 
       finally 
      {
        this.loading = false;
      }
    },

    async deleteCategory(id)
    {
      try
      {
        const response = await axios.post('/api/json.php', {
        type: 'delete-category',
        id: id
        });

        if(response.data.result === 'good')
        {
          //console.log('good!');
          return { success: true, data: response.data };
        }
        else 
        {
          this.error = response.data.message;
          return { success: false, error: this.error };
        }
      }
      catch (error) 
      {
        this.error = error.response?.data?.message || 'Ошибка соединения';
        return { success: false, error: this.error };
      } 
       finally 
      {
        this.loading = false;
      }
    },

    async fetchComments(id)
    {
      try
      {
        const response = await axios.post('/api/json.php', {
          type: 'fetch-comments',
          id: id
        });

        if(response.data.result === 'good')
        {
          return { success: true, data: response.data, count: response.data.count };
        }
        else 
        {
          this.error = response.data.message;
          return { success: false, error: this.error };
        }
      }
      catch(error)
      {
        this.error = error.response?.data?.message || 'Ошибка соединения';
        return { success: false, error: this.error };
      }
    },

    async createComment(product_id, rating, content, date)
    {
      try
      {
        const response = await axios.post('/api/json.php', {
          type: 'create-comment',
          product_id: product_id,
          rating: rating,
          content: content
        });

        if(response.data.result === 'good')
        {
          console.log('good!');
          return { success: true, data: response.data };
        }
        else 
        {
          this.error = response.data.message;
          return { success: false, error: this.error };
        }
      }
      catch(error)
      {
        this.error = error.response?.data?.message || 'Ошибка соединения';
        return { success: false, error: this.error };
      }
    },

    async searchProducts(string, categories, brands)
    {
      const response = await axios.post('/api/json.php', {
        type: 'search-product',
        query: string,
        categories: categories,
        brands: brands
      });

      if(response.data.result === "good")
      {
        return {success: true, data: response.data.data};
      }
      else
      {
        return { success: false, error: this.error };
      }
    },

    async fetchCityies()
    {
      const response = await axios.post('/api/json.php', {type: 'fetch_cityes'});

      if(response.data.result === "good")
      {
        return { success: true, cities: response.data.cities, regions: response.data.regions };
      }
      else
      {
        return { success: false, error: this.error };
      }
    },

    async editPassword(current, new_pass, confirm)
    {
      const response = await axios.post('/api/json.php', {
        type: 'edit-pass',
        current: current,
        new_pass: new_pass,
        confirm: confirm 
      });

      if(response.data.result === "good")
      {
        return { success: true};
      }
      else
      {
        return { success: false, error: this.error };
      }
    },

    async saveAddress(id, type, street, city, postalCode, office, isDefault)
    {
      try
      {
        const response = await axios.post('/api/json.php', {
          type: 'save-address',
          id: id,
          type: type,
          street: street,
          city: city,
          postalCode: postalCode,
          office: office,
          isDefault: isDefault
        });

        if(response.data.result === "good")
        {
          return { success: true };
        }
        else
        {
          return { success: false, error: this.error };
        }
      }
      catch(error)
      {
        return { success: false, error: error.message };
      }
    },

    async addToCart(id)
    {
      /*if(useAuthStore.user != null)
      {*/
        const response = await axios.post('/api/json.php', {
          type: 'add-to-cart',
          id: id
        });

        if(response.data.result === "good")
        {
          return { success: true };
        }
        else
        {
          return { success: false, error: this.error };
        }
      /*}
      else
      {
        console.log('no auth');
      }*/
    },

    async fetchCart()
    {
      const response = await axios.post('/api/json.php', {
        type: 'fetch-cart'
      });

      if(response.data.result == 'good')
      {
        return { success: true, products: response.data.products };
      }
      else
      {
        return { success: false, error: this.error };
      }

      if(response.data.result == 'empty')
      {
        return { success: true, empty: true };
      }
    },

    async removeCart(id)
    {
      const response = await axios.post('/api/json.php', {
        type: 'remove-cart-product',
        id: id
      });

      if(response.data.result == 'good')
      {
        return { success: true };
      }
      else
      {
        return { success: false, error: this.error };
      }
    },

    async fetchProducts()
    {
      try
      {
        const response = await axios.post('/api/json.php', {type: 'get_products'});
        if(response.data.result === 'good')
        {
          //this.products = response.data.products;
          return { success: true, data: response.data.data, count: response.data.count, brands: response.data.brands };
        }
      }
      catch (error) 
      {
        return { success: false, error: this.error };
      } 
      finally 
      {
        this.isLoaded = true;
      }
    },

    async createProduct(name, category, price, oldPrice, stock, description, image, isNew, isPopular)
    { 
      try
      {
        const response = await axios.post('/api/json.php', {
          type: 'create-product',
          name: name,
          category: category,
          price: price,
          oldPrice: oldPrice,
          stock: stock,
          description: description,
          image: image,
          isNew: isNew,
          isPopular: isPopular
        });

        if(response.data.result === 'good')
        {
          console.log('good!');
          return { success: true, data: response.data };
        }
        else 
        {
          this.error = response.data.message;
          return { success: false, error: this.error };
        }
      }
      catch (error) 
      {
        this.error = error.response?.data?.message || 'Ошибка соединения';
        return { success: false, error: this.error };
      } 
       finally 
      {
        this.loading = false;
      }
    },

    async updateProduct(id, name, category, price, oldPrice, stock, description, image, isNew, isPopular)
    {
      try
      {
        const response = await axios.post('/api/json.php', {
          type: 'update-product',
          id: id,
          name: name,
          category: category,
          price: price,
          oldPrice: oldPrice,
          stock: stock,
          description: description,
          image: image,
          isNew: isNew,
          isPopular: isPopular
        });

        if(response.data.result === 'good')
        {
          console.log('good!');
          return { success: true, data: response.data };
        }
        else 
        {
          this.error = response.data.message;
          return { success: false, error: this.error };
        }
      }
      catch (error) 
      {
        this.error = error.response?.data?.message || 'Ошибка соединения';
        return { success: false, error: this.error };
      } 
       finally 
      {
        this.loading = false;
      }
    },

    async deleteProduct(id)
    {
      try
      {
        const response = await axios.post('/api/json.php', {
          type: 'delete-product',
          id: id
        });

        if(response.data.result === 'good')
        {
          console.log('good!');
          return { success: true, data: response.data };
        }
        else 
        {
          this.error = response.data.message;
          return { success: false, error: this.error };
        }
      }
      catch (error) 
      {
        this.error = error.response?.data?.message || 'Ошибка соединения';
        return { success: false, error: this.error };
      } 
       finally 
      {
        this.loading = false;
      }
    },

    // Метод для получения профиля (если нужно проверить сессию)
    async checkAuth() 
    {
      try 
      {
        const response = await axios.post('/api/json.php', { type: 'get_profile' });
        if (response.data.result === 'good') 
        {
          this.user = response.data.user;
        } 
        else 
        {
          this.user = null;
        }
      } 
      catch (error) 
      {
        this.user = null;
      } 
      finally 
      {
        this.isLoaded = true;
      }
    },

    async fetchUsers()
    {
      const response = await axios.post('/api/json.php',
      {
        type: 'fetch-users'
      }
      );

      if(response.data.result === 'good')
      {
        return { success: true, data: response.data.data };
      }
      else
      {
        // Если сервер вернул ошибку
        const errorMessage = response.data.message || 'Ошибка обновления профиля';
        this.error = errorMessage;
        return { success: false, error: errorMessage };
      }
    },

    async deleteUser(id)
    {
      const response = await axios.post('/api/json.php', {
        type: 'delete-user',
        id: id
      });

      if(response.data.result === 'good')
      {
        return { success: true };
      }
      else
      {
        // Если сервер вернул ошибку
        const errorMessage = response.data.message || 'Ошибка обновления профиля';
        this.error = errorMessage;
        return { success: false, error: errorMessage };
      }

    },

    async update_user_info(id, name, surname, email, phone, birthday, bio, notifications, visible)
    {

      const response = await axios.post('/api/json.php', 
        {
            type: 'update-user-info',
            id: id,
            name: name,
            surname: surname,
            email: email,
            phone: phone,
            birthday: birthday,
            bio: bio,
            notifications: notifications,
            visible: visible
        }
      );

      if(response.data.result === 'good')
      {
        return { success: true };
      }
      else
      {
        // Если сервер вернул ошибку
        const errorMessage = response.data.message || 'Ошибка обновления профиля';
        this.error = errorMessage;
        return { success: false, error: errorMessage };
      }
      
    },

    async logout() 
    {
      this.loading = true;
      
      try {
        // Отправляем запрос на выход
        await axios.post('/api/json.php', {
          type: 'logout'
        });
      } catch (error) {
        console.error('Ошибка при выходе:', error);
      } finally {
        // Очищаем состояние
        this.setUser(null);
        
        // Удаляем токен из заголовков
        delete axios.defaults.headers.common['Authorization'];
        
        // Очищаем localStorage
        localStorage.removeItem('user');
        sessionStorage.removeItem('token');
        
        this.loading = false;
      }
    },
    
    // Очистка данных (при ошибках)
    clearAuth() 
    {
      this.user = null;
      this.isLoaded = false;
      localStorage.removeItem('user');
    }
  }
});