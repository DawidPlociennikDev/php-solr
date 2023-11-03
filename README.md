# Usefull commands

Docker list containers
```bash
    docker ps
```

Go into SOLR container as root
```bash
    docker exec -u 0:0 -it {docker_id} /bin/bash
```

Run SOLR Cloud
```bash
    bin/solr start -e cloud -force
```

Seed SOLR collection from file
```bash
    bin/post -c {collection_name} example/exampledocs/*
```
    
Create new SOLR collection
```bash
    bin/solr create -c {collection_name} -s 2 -rf 2 -force
```

Stop SOLR nodes
```bash
    bin/solr stop -all
```

Diagnostics collection
```bash
    bin/solr healthcheck -c {collection_name}
```

## Udemy course

Solr pomija w wyszukiwaniu zbędne słowa (and, the, an etc.)
Solr rozumie synonimy
Szybko dopasowuje zapytanie do rekordów i sprawdza stopień dopasowania szukanej frazy do rekordów
Jeden dokument może mieć różne pola 

q - to jest warunek (name:book)
fq - to jest kolejny warunek (inStock:true)
sort - sortowanie (price asc)
start, rows - paginacja
fl - wybierz, które kolumny wyświetlić
df - szukanie w konkretnej kolumnie (name przeszuka tylko słowo kluczowe z q w kolumnach name)
wt - format w jakim solr ma wyświetlić odpowiedź


CURL - get documents
```bash
    curl http://localhost:8983/solr/techproducts/select?df=name&indent=true&q.op=OR&q=black
```

nowe konfiguracje tworzy się w ścieżce server/solr/configsets
przykład:
```bash
    cp -r _default/. search_twitter/
```


Get current schema fields
```bash
    curl -X GET "http://localhost:8983/solr/search_twitter/schema/fields"
``` 

Add document to solr
```bash
  curl -X POST -H 'Content-Type: application/json' 'http://localhost:8983/solr/search_twitter/update' --data-binary '
  {
    "add": {
      "doc": {
          "content":"Late night with Solr 8.5",
          "likes":10
    }
    }
  }'
```
```bash
  curl -X POST -H 'Content-Type: application/json' 'http://localhost:8983/solr/search_twitter/update?commitWithin=100' --data-binary '
  {
    "add": {
      "doc": {
          "twitter_id":"4",
          "user_name":"John",
          "lang":"eng",
          "content":"Happy searching!",
          "likes_count":10
    }
    }
  }'
```

jeżeli ustawimy atrybut pola **indexed** i **docValues** na false to SOLR nie będzie przeszukiwać po tych polach po żadaniu

**stored** jeżeli ustawimy na false to możemy przeszukiwać po danej kolumnie, ale nie będzie ona w responsie

**multiValued** pozwala na przechowywanie wielu informacji w jednej kolumnie ['info1', 'info2', 'info3']


Creat field 'new_field' on base 'user_name' field
```bash
  curl -X POST -H 'Content-type:application/json' --data-binary '{
    "add-copy-field":{
      "source":"new_field",
      "dest":[ "user_name" ]}
  }' http://localhost:8983/api/cores/search_twitter/schema
```

Delete all documents
```bash
  curl -X POST -H 'Content-Type: application/json' 'http://localhost:8983/solr/search_twitter/update?commitWithin=100' --data-binary '
  {
    "delete": {"query": "*:*"}
  }'

```

Add an array of documents
```bash
  curl -X POST -H 'Content-Type: application/json' 'http://localhost:8983/solr/search_twitter/update?commitWithin=100' --data-binary '
  {
    "add": {
      "overwrite": false, 
      "commitWithin": 5000,
        "doc": {
            "twitter_id" : "9",
            "user_name_string" : "Solr",
            "type_string" : "post",
            "lang_string" : "en",
            "updated_on_pdt" : "2019-12-30T09:30:22Z",
            "likes_count_pint" : 10,
            "text_en" : "Happy Searching!",
            "link_strings" : ["https://github.com/apache/lucene-solr",
                        "https://lucene.apache.org/solr/"]
        }
    },
    "add": {
        "doc": {
            "twitter_id" : "10",
            "user_name_string" : "Solr",
            "type_string" : "post",
            "lang_string" : "en",
            "updated_on_pdt" : "2019-12-30T09:30:22Z",
            "likes_count_pint" : 10,
            "text_en" : "Happy Searching!",
            "link_strings" : ["https://github.com/apache/lucene-solr",
                        "https://lucene.apache.org/solr/"]
        }
    },
    "add": {
        "doc": {
            "twitter_id" : "11",
            "user_name_string" : "Solr",
            "type_string" : "post",
            "lang_string" : "en",
            "updated_on_pdt" : "2019-12-30T09:30:22Z",
            "likes_count_pint" : 10,
            "text_en" : "Happy Searching!",
            "link_strings" : ["https://github.com/apache/lucene-solr",
                        "https://lucene.apache.org/solr/"]
        }
    }
  }'
```

Delete document by id
```bash
  curl -X POST -H 'Content-Type: application/json' 'http://localhost:8983/solr/search_twitter/update?commitWithin=100' --data-binary '
  {
    "delete": ["9","10"]
  }'
```

Update a document
```bash
  curl -X POST -H 'Content-Type: application/json' 'http://localhost:8983/solr/search_twitter/update?commitWithin=100' --data-binary '
  [{
      "twitter_id" : "2",	    
      "likes_count_pint" : {"set":11}	      
  }]'
```

Update a document by version
```bash
  curl -X POST -H 'Content-Type: application/json' 'http://localhost:8983/solr/search_twitter/update?commitWithin=100' --data-binary '
  [{
      "twitter_id" : "2",	    
      "likes_count_pint" : {"set":11}, "_version_":1779646351273885696	      
  }]'
```

Add nested documents
```bash
    curl -X POST -H 'Content-Type: application/json' 'http://localhost:8983/solr/search_twitter/update?commitWithin=100' --data-binary '
    [{
      "id": "5",
      "title_txt": "New Lucene and Solr release is out",
      "type_string": "post",
      "_childDocuments_": [
        {
          "id": "6",
          "type_string": "comment",
          "content_txt": "Lots of new features"
        }
      ]
    },
    {
      "id": "7",
      "title_txt": "Solr adds join support",
      "type_string": "post",
      "comments_string": [{
          "twitter_id": "8",
          "type_string": "comment",
          "content_txt": "SolrCloud supports it too!"
        },
        {
          "id": "9",
          "type_string": "comment",
          "content_txt": "New filter syntax"
        }
      ]
    }
  ]'
```

Solr dopasowuje słowa do siebie tj. 
- sweeet -> sweet, 
- running -> run -> runnable
- ApacheSolr -> Apache Solr -> Apache-Solr
 ## TODO

 1. problem z tworzeniem core/schematów 
