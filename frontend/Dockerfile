FROM node:22-bullseye

WORKDIR /app

COPY package*.json ./

# Ставим все зависимости включая devDependencies
RUN npm install

COPY . .

# Используем npx для локального vite
CMD ["npx", "vite", "dev", "--host"]