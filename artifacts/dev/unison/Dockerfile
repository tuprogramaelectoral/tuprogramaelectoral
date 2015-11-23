FROM popsikle/unison

RUN rm /etc/service/unison/run
VOLUME /var/www
WORKDIR /var/www
EXPOSE 5000

CMD /bin/unison-2.48.3 -socket 5000 -repeat watch
