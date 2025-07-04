FROM node:18-alpine

WORKDIR /usr/src/app

COPY package.json package-lock.json ./

RUN npm install --only=production

COPY src/ .

EXPOSE 4000
CMD ["node","server.js"]