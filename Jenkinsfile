pipeline{
    agent { label '!windows' }
    stages {
        stage('SonarQube') {
            steps{
                script { scannerHome = tool 'SonarQubeScanner' }
                withSonarQubeEnv('SonarQubeScanner') {
                    sh "${scannerHome}/bin/sonar-scanner -Dsonar.projectKey=OWS -Dsonar.login=admin -Dsonar.password=admin1 "
                }
            }
        }
        stage('Build') {
            steps {
                echo 'building'
            }
        }
        stage('Test') {
            steps {
                echo 'Testing'
            }
        }
        stage('Deploy') {
            steps {
                echo 'Deploying'
            }
        }
    }
}
