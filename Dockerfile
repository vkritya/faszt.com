ARG VERSION=latest-alpine

FROM node:lts-alpine AS build
RUN mkdir -p /home/node/app && chown -R node:node /home/node/app
WORKDIR /home/node/app

USER node

COPY --chown=node:node ./app .
RUN npm install
RUN npm run build


FROM ghcr.io/librespeed/speedtest:$VERSION AS app

COPY ./app/entrypoint.sh /
COPY --from=build /home/node/app/dist /speedtest/
RUN ls /speedtest/
RUN mv /speedtest/index.php /speedtest/ui.php

ENV DISABLE_IPINFO=true
ENV TITLE=Faszt.com
