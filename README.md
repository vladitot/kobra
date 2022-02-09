# как с этим работать

Я рекомендую прописать себе в ~/.zshrc

`alias kobra-dev="php /Users/vladitot/IdeaProjects/kobra-php/kobra"`

только путь поправьте пожалуйста до папки со своим проектом

и тогда вы сможете в других проектах использовать kobra

## Сбилдить в в систему
выполните `make load`

этв  штука поместить собранный kobra в /usr/local/bin/kobra и позволит запускать прямо так `kobra install` например

## php 8.0 понадобится
brew install php@8.0

## чтобы php завелся захерачь в .zshrc
`export PATH=/usr/local/opt/php@8.0/bin:$PATH`


