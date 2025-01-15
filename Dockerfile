FROM amazonlinux:2023 AS build

ARG ARGS

COPY ./files /files

RUN bash /files/build.sh $ARGS

FROM scratch

COPY --from=build / /

WORKDIR /var/www/sample/src

EXPOSE 80
EXPOSE 3306

ENTRYPOINT [ "sh", "/usr/bin/startup.sh" ]
