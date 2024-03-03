# article-agregator
Test ArticleAgretator

## Install

- Clone github project

```
git clone https://github.com/malejulien013/article-agregator.git
```

- Build and launch docker-compose project

```
docker-compose build
docker-compose up -d
```

- Access Front on url https://localhost
- Navigate to *Sources* to add a new source (database, rss, file)
- Navigate to *Articles* to read articles

### Annexes

- Docker environment includes 2 MySQL databases


- Project database : 
  - host : *localhost*
  - port : *3306*
  - database name : *app*
  - username : *root*
  - password : *nopassword*


- Test database (acts like external DB to agregate articles) :
  - host : *localhost*
  - port : *3307*
  - database name : *app*
  - username : *root*
  - password : *nopassword*


### Source examples

- Name : Local DB
- Type : database
- Parameters: pdo-mysql://app:!ChangeMe!@mysql_test:3306/app?serverVersion=8.0&charset=utf8mb4


- Name : Le Monde
- Type : rss 
- Parameters : http://www.lemonde.fr/rss/une.xml


- Name : France24.com / France
- Type : rss
- Parameters : https://www.france24.com/fr/france/rss

- Name : CSV File
- Type : file
- Parameters : file accessible in /var/test.csv