FROM ruby
RUN apt-get -y update && apt-get -y install libicu-dev
RUN gem install gollum
RUN gem install redcarpet org-ruby

VOLUME /var/www
WORKDIR /var/www

CMD ["gollum", "--port", "80", "--page-file-dir", "docs"]
EXPOSE 80
