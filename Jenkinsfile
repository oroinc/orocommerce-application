pipeline {
    environment {
        ORO_BASELINE_VERSION = '5.1-latest'
        ORO_VER = '5.1.0'
        ORO_GIT_REPOSITORY = 'https://github.com/oroinc/orocommerce-application.git'
        ORO_APP = env.ORO_GIT_REPOSITORY.replaceAll('https://github.com/', '').replaceAll('.git', '')
        ORO_DOCKER_PROJECT = 'oroinc'
        ORO_SAMPLE_DATA = 'y'
    }
    agent {
        node {
            label 'docker1'
        }
    }
    options {
        timeout(time: 1, unit: 'HOURS')
        buildDiscarder(logRotator(artifactDaysToKeepStr: '', artifactNumToKeepStr: '', daysToKeepStr: '150', numToKeepStr: '50'))
        disableResume()
        skipStagesAfterUnstable()
        timestamps ()
    }
    stages {
        stage('Init') {
            steps {
                script {
                    checkout([
                        $class: 'GitSCM',
                        branches: [[name: env.ORO_VER]],
                        extensions: [[$class: 'RelativeTargetDirectory', relativeTargetDir: env.ORO_APP]],
                        userRemoteConfigs: [[credentialsId: 'jenkins-oro-app', url: env.ORO_GIT_REPOSITORY ]]
                    ])
                    defaultVariables = readProperties(interpolate: true, file: "$WORKSPACE/docker/compose/.env")
                    readProperties(interpolate: true, defaults: defaultVariables + [ORO_IMAGE_TAG: env.BUILD_TAG], file: "$WORKSPACE/$ORO_APP/.env-build").each {key, value -> env[key] = value }
                    sh '''
                        printenv | sort
                        rm -rf /dev/shm/${EXECUTOR_NUMBER}* ||:
                        cp -rf $WORKSPACE /dev/shm/${EXECUTOR_NUMBER}
                    '''
                }
            }
        }
        stage('Build') {
            parallel {
                stage('Build:prod') {
                    stages {
                        stage('Build:prod:source') {
                            environment {
                                GITHUB_TOKEN = credentials('jenkins-oroinc-app')
                                GITLAB_TOKEN = credentials('orocrmdeployer-gitlab')
                            }
                            steps {
                                dir("/dev/shm/${EXECUTOR_NUMBER}/$ORO_APP") { sh '''COMPOSER_AUTH="{\\\"http-basic\\\": {\\\"github.com\\\": {\\\"username\\\": \\\"$GITHUB_TOKEN_USR\\\", \\\"password\\\": \\\"$GITHUB_TOKEN_PSW\\\"}}, \\\"gitlab-oauth\\\": {\\\"$ORO_GITLAB_DOMAIN\\\": \\\"$GITLAB_TOKEN_PSW\\\"}}" ../../docker/ci/composer.sh -b $ORO_BASELINE_VERSION -- '--no-dev install' '''}
                            }
                        }
                        stage('Build:prod:image') {
                            environment {
                                ORO_REGISTRY_CREDS = credentials('harborio.oro.cloud')
                            }
                            steps {
                                dir("/dev/shm/${EXECUTOR_NUMBER}/$ORO_APP") { sh '''
                                    echo $ORO_REGISTRY_CREDS_PSW | docker login -u $ORO_REGISTRY_CREDS_USR --password-stdin harborio.oro.cloud
                                    docker buildx build --load --pull --rm --build-arg ORO_BASELINE_VERSION --build-arg ORO_IMAGE_FROM=$ORO_DOCKER_PROJECT/runtime -t ${ORO_IMAGE,,}:$ORO_IMAGE_TAG -f "../../docker/image/application/Dockerfile" .
                                '''}
                            }
                        }
                        stage('Build:prod:install:de') {
                            environment {
                                ORO_LANGUAGE = 'de_DE'
                                ORO_FORMATTING_CODE = 'de_DE'
                            }
                            steps {
                                dir("/dev/shm/${EXECUTOR_NUMBER}/$ORO_APP") {
                                    sh '''
                                        docker compose -p prod_${EXECUTOR_NUMBER} --project-directory ../../docker/compose -f ../../docker/compose/compose-orocommerce-application.yaml down -v
                                        docker compose -p prod_${EXECUTOR_NUMBER} --project-directory ../../docker/compose -f ../../docker/compose/compose-orocommerce-application.yaml up --quiet-pull --exit-code-from install install
                                        rm -rf ../../docker/image/application/public_storage
                                        rm -rf ../../docker/image/application/private_storage
                                        docker cp prod_${EXECUTOR_NUMBER}-install-1:/var/www/oro/public/media/ ../../docker/image/application/public_storage
                                        docker cp prod_${EXECUTOR_NUMBER}-install-1:/var/www/oro/var/data/ ../../docker/image/application/private_storage
                                        ORO_IMAGE_INIT=${ORO_IMAGE_INIT,,}-de DB_IP=$(docker inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' prod_${EXECUTOR_NUMBER}-db-1) docker compose -p prod_${EXECUTOR_NUMBER} --project-directory ../../docker/compose  -f ../../docker/compose/compose-orocommerce-application.yaml up --build --quiet-pull --exit-code-from backup backup
                                    '''
                                }
                            }
                        }
                        stage('Build:prod:install:fr') {
                            environment {
                                ORO_LANGUAGE = 'fr_FR'
                                ORO_FORMATTING_CODE = 'fr_FR'
                            }
                            steps {
                                dir("/dev/shm/${EXECUTOR_NUMBER}/$ORO_APP") {
                                    sh '''
                                        docker compose -p prod_${EXECUTOR_NUMBER} --project-directory ../../docker/compose -f ../../docker/compose/compose-orocommerce-application.yaml down -v
                                        docker compose -p prod_${EXECUTOR_NUMBER} --project-directory ../../docker/compose -f ../../docker/compose/compose-orocommerce-application.yaml up --quiet-pull --exit-code-from install install
                                        rm -rf ../../docker/image/application/public_storage
                                        rm -rf ../../docker/image/application/private_storage
                                        docker cp prod_${EXECUTOR_NUMBER}-install-1:/var/www/oro/public/media/ ../../docker/image/application/public_storage
                                        docker cp prod_${EXECUTOR_NUMBER}-install-1:/var/www/oro/var/data/ ../../docker/image/application/private_storage
                                        ORO_IMAGE_INIT=${ORO_IMAGE_INIT,,}-fr DB_IP=$(docker inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' prod_${EXECUTOR_NUMBER}-db-1) docker compose -p prod_${EXECUTOR_NUMBER} --project-directory ../../docker/compose  -f ../../docker/compose/compose-orocommerce-application.yaml up --build --quiet-pull --exit-code-from backup backup
                                    '''
                                }
                            }
                        }
                        stage('Build:prod:install:en') {
                            steps {
                                dir("/dev/shm/${EXECUTOR_NUMBER}/$ORO_APP") {
                                    sh '''
                                        docker compose -p prod_${EXECUTOR_NUMBER} --project-directory ../../docker/compose -f ../../docker/compose/compose-orocommerce-application.yaml down -v
                                        docker compose -p prod_${EXECUTOR_NUMBER} --project-directory ../../docker/compose -f ../../docker/compose/compose-orocommerce-application.yaml up --quiet-pull --exit-code-from install install
                                        rm -rf ../../docker/image/application/public_storage
                                        rm -rf ../../docker/image/application/private_storage
                                        docker cp prod_${EXECUTOR_NUMBER}-install-1:/var/www/oro/public/media/ ../../docker/image/application/public_storage
                                        docker cp prod_${EXECUTOR_NUMBER}-install-1:/var/www/oro/var/data/ ../../docker/image/application/private_storage
                                        DB_IP=$(docker inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' prod_${EXECUTOR_NUMBER}-db-1) docker compose -p prod_${EXECUTOR_NUMBER} --project-directory ../../docker/compose  -f ../../docker/compose/compose-orocommerce-application.yaml up --build --quiet-pull --exit-code-from backup backup
                                    '''
                                }
                            }
                        }
                    }
                }
            }
        }
        stage('Push') {
            environment {
                KEY_FILE = credentials('jenkins_oro-product-development_iam_gserviceaccount_com')
                configuration = 'oro-product-development'
                credentials = "--configuration ${configuration}"
            }
            steps {
                sh '''
                    gcloud config configurations list | grep ^${configuration} -q || gcloud config configurations create ${configuration}
                    gcloud config configurations activate ${configuration}
                    gcloud -q ${credentials} auth activate-service-account --key-file "$KEY_FILE" --project ${configuration}
                    gcloud ${credentials} auth configure-docker
                    set -x
                    docker image ls
                    docker image push ${ORO_IMAGE,,}:$ORO_IMAGE_TAG
                    docker image push ${ORO_IMAGE_INIT,,}:$ORO_IMAGE_TAG
                    docker image push ${ORO_IMAGE_INIT,,}-de:$ORO_IMAGE_TAG
                    docker image push ${ORO_IMAGE_INIT,,}-fr:$ORO_IMAGE_TAG
                    docker image rm -f ${ORO_IMAGE,,}:$ORO_IMAGE_TAG ||:
                    docker image rm -f ${ORO_IMAGE_INIT,,}:$ORO_IMAGE_TAG ||:
                    docker image rm -f ${ORO_IMAGE_INIT,,}-de:$ORO_IMAGE_TAG ||:
                    docker image rm -f ${ORO_IMAGE_INIT,,}-fr:$ORO_IMAGE_TAG ||:
                    docker image prune -f
                '''
            }
        }
    }
    post {
        always {
            sh '''
                rm -rf "logs"
                mkdir -p "logs"
                cp -rfv "/dev/shm/${EXECUTOR_NUMBER}_1/$ORO_APP/var/logs/"* "logs"/ ||:
                printenv | grep ^ORO | sort | sed -e 's/=/="/;s/\$/"/' > "logs"/env-config
                docker ps -a -f "name=.*_.*-.*" > logs/docker_ps.txt ||:
                docker ps -a --format '{{.Names}}' -f "name=.*_.*-.*" | xargs -r -I {} bash -c "docker logs {} > logs/docker_logs_{}.txt 2>&1" ||:
            '''
            dir("logs") {
                archiveArtifacts defaultExcludes: false, allowEmptyArchive: true, artifacts: '**', excludes: '**/*.sql', caseSensitive: false
                junit allowEmptyResults: true, testResults: "junit/*.xml"
            }
        }
    }
}

