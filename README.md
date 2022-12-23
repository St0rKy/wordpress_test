#   Install

* Download Docker Desktop from https://www.docker.com/products/docker-desktop/
* Clone this repo and navigate to the project folder
* Run ```docker-compose up```
* Navigate to ```src/plugins/pop-aurelian``` 
* Run ```composer install```
* Run ```npm install```
* Run ```npm run watch``` for the watcher or ```npm run build``` for the final build
* If the watcher is run ```http://localhost:3000``` should be opened automatically on your browser
* Follow the wordpress install steps then activate the ```Pop Aurelian``` plugin
* To enable the cypress tests copy ```cypress.env.example.json``` to ```cypress.env.json``` and fill in your username and password created on wordpress install

# Run tests

* After the ```composer install``` command on commit the cypress tests and coding standards are automatically run
* If you want to run the cypress tests manually navigate to ```src/plugins/pop-aurelian``` and run ```npx cypress open``` (make sure ```cypress.env.json``` was created otherwise the tests will fail)