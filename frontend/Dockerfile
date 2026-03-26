FROM node:22-bullseye

WORKDIR /app

# Копируем package файлы
COPY package*.json ./

# Устанавливаем зависимости
RUN npm install

# Копируем остальные файлы
COPY . .

# Создаем папку для node_modules если нужно
RUN chown -R node:node /app

# Переключаемся на пользователя node (безопасность)
USER node

# Открываем порт Vite
EXPOSE 5173

# Запускаем Vite с явными параметрами
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0", "--port", "5173"]