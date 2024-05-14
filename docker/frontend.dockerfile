# Stage 1: Build the Vue.js application
FROM node:18-alpine as build-stage

WORKDIR /app

# Copiar os arquivos da pasta frontend para o diretório de trabalho
COPY frontend/package*.json ./
RUN npm install

COPY frontend .

# Rodar o build
RUN npm run build

# Stage 2: Serve the application with nginx
FROM nginx:stable-alpine as production-stage

COPY --from=build-stage /app/dist /usr/share/nginx/html

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
