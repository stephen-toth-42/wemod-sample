#!/bin/bash

set -x
set -e

cd /files


dnf remove -y vim-minimal
dnf update -y
dnf install --allowerasing -y wget curl vim libxslt git zip unzip httpd procps-ng \
    php8.2 php8.2-cli php8.2-mbstring php8.2-opcache php8.2-mysqlnd php8.2-xml php8.2-fpm php8.2-zip \
    freetype libX11 libXext libXrender libjpeg libpng openssl xorg-x11-fonts-75dpi xorg-x11-fonts-Type1 \
    openssh lftp mod_fcgid findutils cronie inotify-tools bash-completion make mariadb105-server


mv composer.phar /usr/bin/composer
chmod 755 /usr/bin/composer

mkdir -p /var/www/sample
mkdir -p /var/www/sample/src
mkdir -p /var/www/sample/conf
mkdir -p /var/www/sample/data

ln -s /usr/bin/vim /usr/bin/vi
git config --global --add safe.directory /var/www/sample/src

cat <<EOF >/root/.vimrc
set nofixendofline
set shiftwidth=4
set tabstop=4
set expandtab
set autoindent
vnoremap > >gv
vnoremap < <gv
set background=dark
set hlsearch
EOF

cat <<EOF >/root/.bashrc
parse_git_branch() {
     git branch --show-current 2> /dev/null | sed -e 's/^.*$/ (&)/'
}
PS1="\[\e]0;\u@\h: \w\a\]${debian_chroot:+($debian_chroot)}\[\033[01;32m\]\u@\h\[\033[00m\]:\[\033[01;34m\]\w\[\033[33m\]\\\$(parse_git_branch)\[\033[00m\]\$ "
# enable color support of ls and also add handy aliases
if [ -x /usr/bin/dircolors ]; then
    test -r ~/.dircolors && eval "$(dircolors -b ~/.dircolors)" || eval "$(dircolors -b)"
    alias ls='ls --color=auto'
    #alias dir='dir --color=auto'
    #alias vdir='vdir --color=auto'

    alias grep='grep --color=auto'
    alias fgrep='fgrep --color=auto'
    alias egrep='egrep --color=auto'
fi
alias ll='ls -alF'
alias la='ls -A'
alias l='ls -CF'
EOF

cp *.conf /var/www/sample/conf/.

cp startup.sh /usr/bin/startup.sh
chmod 755 /usr/bin/startup.sh

cd /
rm -rf /files

dnf remove -y gcc gcc-c++
dnf autoremove
dnf list installed
dnf clean all
rm -rf /var/cache/yu || true
rm -rf /tmp/* || true
rm -rf /tmp/.* || true
rm -rf /var/cache/* || true
rm -rf /var/lib/yum/history/* || true
rm -rf /var/lib/yum/yumdb/* || true
rm -rf /var/lib/dnf/* || true
rm -rf /var/log/* || true
rm -rf  /usr/lib/.build-id/* || true

find / -printf "%s\t%p\n" | sort -hr
