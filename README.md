# как с этим работать

Я рекомендую прописать себе в ~/.zshrc

alias kobra-update="docker pull vladitot/kobra:latest

alias kobra="docker run --rm -it -v `pwd`/:/var/www:ro -v `pwd`/infra/:/var/www/infra -v ~/.ssh/id_rsa:/tmp/.ssh/id_rsa:ro vladitot/kobra"

И тогда теоретически, ты сможешь в папке своего проекта использовать kobra
