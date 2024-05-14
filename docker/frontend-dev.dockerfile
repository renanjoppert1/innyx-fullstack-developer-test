# Stage 1: Build the Vue.js application
FROM node:18-alpine as build-stage

WORKDIR /app

# Copiar os arquivos da pasta frontend para o diretório de trabalho
COPY frontend/package*.json ./

RUN npm install

COPY frontend .

EXPOSE 8080

CMD ["npm", "run", "serve"]
