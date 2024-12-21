FROM node:18-alpine

WORKDIR /app

RUN npm install -g pnpm

COPY ./shared/workspace/front/package.json .

RUN pnpm install

COPY ./shared/workspace/front .

EXPOSE 5050

CMD ["pnpm", "run", "dev"]