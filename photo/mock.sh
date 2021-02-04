#!/usr/bin/env zsh

rm -rf ./dirs
mkdir dirs && cd ./dirs
ROOT_DIR=$(pwd)
mkdir "Женские пальто"
mkdir "Женские пальто/А001"
mkdir "Женские пальто/А002 "
mkdir "Женские пальто/ А003"
mkdir "Женские пальто/А001/Желтый"
mkdir "Женские пальто/А001/Белый "
mkdir "Женские пальто/А001/ Черный"
mkdir "Женские пальто/А001/Бежевый   Melange  123090 "
mkdir "Женские пальто/А001/Бежевый  Melange  123098"
mkdir "Женские пальто/А001/ Бежевый   Melange  123092 "

cd "$ROOT_DIR/Женские пальто/А001/Белый /"
touch "1.jpg" "12.jpg" "affdsa.jpg"

cd "$ROOT_DIR/Женские пальто/А001/ Черный/"
touch "1аы.jpg" "2.jpg" "121-ывып.jpg" "affaafzxzx21.-1dsa6.jpg" "А001-Черный-1.jpg" "А001-Черный-2.jpg"