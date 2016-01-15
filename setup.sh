docker rm smartmember
docker build -t smartmember .

docker run -it -p 80:80 --name smartmember  -v $PWD/../:/var/www/html/ smartmember /bin/bash


