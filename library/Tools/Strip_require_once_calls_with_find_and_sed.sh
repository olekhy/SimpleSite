$ cd path/to/ZendFramework/library
$ find . -name '*.php' -print0 | xargs -0 \
  sed --regexp-extended --in-place 's/(require_once)/\/\/ \1/g'