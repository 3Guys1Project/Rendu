FROM node:18-alpine

WORKDIR /app

RUN npm install -g pnpm

COPY ./shared/workspace/front/package.json .

RUN pnpm install

COPY ./shared/workspace/front .

EXPOSE 5050

COPY ./shared/workspace/front/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]