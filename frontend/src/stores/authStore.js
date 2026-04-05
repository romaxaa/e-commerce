import { defineStore } from 'pinia';
import axios from 'axios';
import { useRouter } from 'vue-router'

const router = useRouter();

export const useAuthStore = defineStore('auth', {
  state: () => ({
    //переменные пользователя
    user: null, // Изначально данных нет
    isLoaded: false,
    loading: false,
    error: null,

    //переменные товаров
    products: null,

    //переменные категорий
    categories: null
  }),

  getters: {
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
      if (userData) {
        localStorage.setItem('user', JSON.stringify(userData));
      } else {
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
          console.log('good!');
          return { success: false, error: this.error };
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
          console.log('user', response.data.user);
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

    async products()
    {
      try
      {
        const response = await axios.post('/api/json.php', {type: 'get_products'});
        if(response.data.result === 'good')
        {
          this.products = response.data.products;
          return { success: true, products: this.products };
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

    async categories()
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

    async update_user_info(name, surname, email, phone, birthday, bio)
    {
      try
      {
        const response = await axios.post('/api/json.php', 
          {
            type: 'update-user-info',
              name: name,
              surname: surname,
              email: email,
              phone: phone,
              birthday: birthday,
              bio: bio
          }
        );

        if(response.data.result === 'good')
        {
          return { success: true, categories: this.categories };
        }
        else
        {
          // Если сервер вернул ошибку
          const errorMessage = response.data.message || 'Ошибка обновления профиля';
          this.error = errorMessage;
          return { success: false, error: errorMessage };
        }
      }
      catch(error)
      {
        // Обработка ошибок сети/сервера
        let errorMessage = 'Произошла ошибка при обновлении профиля';
        
        if (error.response) {
          // Сервер ответил с ошибкой
          errorMessage = error.response.data?.message || `Ошибка ${error.response.status}`;
        } else if (error.request) {
          // Запрос был отправлен, но ответ не получен
          errorMessage = 'Нет соединения с сервером';
        } else {
          // Ошибка при настройке запроса
          errorMessage = error.message;
        }
        
        this.error = errorMessage;
        return { success: false, error: errorMessage };
      }
      finally
      {
        this.isLoaded = true;
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