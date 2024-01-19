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
}
}
