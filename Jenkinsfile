pipeline {
    environment {
        ORO_BASELINE_VERSION = '5.1-latest'
        ORO_BEHAT_OPTIONS = '--skip-isolators'
        ORO_BEHAT_TAGS = '@e2esmokeci'
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
        timestamps ()
        ansiColor('xterm')
    }
    stages {
        stage('Init') {
            steps {
                script {
                    try {
                        retry(5) {
                            checkout([
                                $class: 'GitSCM',
                                branches: [[name: 'master']],
                                extensions: [[$class: 'RelativeTargetDirectory', relativeTargetDir: ".build"]],
                                userRemoteConfigs: [[url: 'https://github.com/oroinc/docker-build.git']]
                            ])
                        }
                    } catch (error) {
                        error message:"ERROR: Cannot perform git checkout!, Reason: '${error}'"
                    }
                    defaultVariables = readProperties(interpolate: true, file: "$WORKSPACE/.build/docker-compose/.env")
                    readProperties(interpolate: true, defaults: defaultVariables + [ORO_IMAGE_TAG: env.BUILD_TAG], file: "$WORKSPACE/.env-build").each {key, value -> env[key] = value }
                    sh '''
                        printenv | sort
                        rm -rf $WORKSPACE/../$BUILD_TAG ||:
                        cp -rf $WORKSPACE $WORKSPACE/../$BUILD_TAG
                    '''
                }
            }
        }
        stage('Build') {
            parallel {
                stage('Build:prod') {
                    stages {
                        stage('Build:prod:source') {
                            steps {
                                sh '''COMPOSER_PROCESS_TIMEOUT=600 .build/scripts/composer.sh -b $ORO_BASELINE_VERSION -- '--no-dev install' '''
                            }
                        }
                        stage('Build:prod:image') {
                            steps {
                                sh '''
                                    docker buildx build --pull --load --rm --build-arg ORO_BASELINE_VERSION -t ${ORO_IMAGE,,}:$ORO_IMAGE_TAG -f ".build/docker/Dockerfile" .
                                '''
                            }
                        }
                        stage('Build:prod:install:de') {
                            environment {
                                ORO_LANGUAGE = 'de_DE'
                                ORO_FORMATTING_CODE = 'de_DE'
                            }
                            steps {
                                sh '''
                                    docker compose -p prod_${EXECUTOR_NUMBER} --project-directory .build/docker-compose -f .build/docker-compose/compose-orocommerce-application.yaml down -v
                                    docker compose -p prod_${EXECUTOR_NUMBER} --project-directory .build/docker-compose -f .build/docker-compose/compose-orocommerce-application.yaml up --exit-code-from install --quiet-pull install
                                    rm -rf .build/docker/public_storage
                                    rm -rf .build/docker/private_storage
                                    docker cp prod_${EXECUTOR_NUMBER}-install-1:/var/www/oro/public/media/ .build/docker/public_storage
                                    docker cp prod_${EXECUTOR_NUMBER}-install-1:/var/www/oro/var/data/ .build/docker/private_storage
                                    ORO_IMAGE_INIT=${ORO_IMAGE_INIT,,}-de DB_IP=$(docker inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' prod_${EXECUTOR_NUMBER}-db-1) docker compose -p prod_${EXECUTOR_NUMBER} --project-directory .build/docker-compose  -f .build/docker-compose/compose-orocommerce-application.yaml up --build --quiet-pull --exit-code-from backup backup
                                '''
                            }
                        }
                        stage('Build:prod:install:fr') {
                            environment {
                                ORO_LANGUAGE = 'fr_FR'
                                ORO_FORMATTING_CODE = 'fr_FR'
                            }
                            steps {
                                sh '''
                                    docker compose -p prod_${EXECUTOR_NUMBER} --project-directory .build/docker-compose -f .build/docker-compose/compose-orocommerce-application.yaml down -v
                                    docker compose -p prod_${EXECUTOR_NUMBER} --project-directory .build/docker-compose -f .build/docker-compose/compose-orocommerce-application.yaml up --exit-code-from install --quiet-pull install
                                    rm -rf .build/docker/public_storage
                                    rm -rf .build/docker/private_storage
                                    docker cp prod_${EXECUTOR_NUMBER}-install-1:/var/www/oro/public/media/ .build/docker/public_storage
                                    docker cp prod_${EXECUTOR_NUMBER}-install-1:/var/www/oro/var/data/ .build/docker/private_storage
                                    ORO_IMAGE_INIT=${ORO_IMAGE_INIT,,}-fr DB_IP=$(docker inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' prod_${EXECUTOR_NUMBER}-db-1) docker compose -p prod_${EXECUTOR_NUMBER} --project-directory .build/docker-compose  -f .build/docker-compose/compose-orocommerce-application.yaml up --build --quiet-pull --exit-code-from backup backup
                                '''
                            }
                        }
                        stage('Build:prod:install:en') {
                            steps {
                                sh '''
                                    docker compose -p prod_${EXECUTOR_NUMBER} --project-directory .build/docker-compose -f .build/docker-compose/compose-orocommerce-application.yaml down -v
                                    docker compose -p prod_${EXECUTOR_NUMBER} --project-directory .build/docker-compose -f .build/docker-compose/compose-orocommerce-application.yaml up --quiet-pull --exit-code-from install install
                                    rm -rf .build/docker/public_storage
                                    rm -rf .build/docker/private_storage
                                    docker cp prod_${EXECUTOR_NUMBER}-install-1:/var/www/oro/public/media/ .build/docker/public_storage
                                    docker cp prod_${EXECUTOR_NUMBER}-install-1:/var/www/oro/var/data/ .build/docker/private_storage
                                    DB_IP=$(docker inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' prod_${EXECUTOR_NUMBER}-db-1) docker compose -p prod_${EXECUTOR_NUMBER} --project-directory .build/docker-compose -f .build/docker-compose/compose-orocommerce-application.yaml build backup
                                '''
                            }
                        }
                    }
                }
                stage('Build:test') {
                    environment {
                        ORO_TESTS_PATH = 'src'
                    }
                    stages {
                        stage('Build:test:source') {
                            steps {
                                dir("$WORKSPACE/../$BUILD_TAG") {
                                    sh '''COMPOSER_PROCESS_TIMEOUT=600 .build/scripts/composer.sh -b $ORO_BASELINE_VERSION '''
                                    // sh '.build/scripts/test_php-cs-fixer.sh -b $ORO_BASELINE_VERSION'
                                    sh '.build/scripts/test_phpcs.sh -b $ORO_BASELINE_VERSION'
                                    sh '.build/scripts/test_phpmd.sh -b $ORO_BASELINE_VERSION'
                                }
                            }
                        }
                        stage('Build:test:unit') {
                            steps {
                                dir("$WORKSPACE/../$BUILD_TAG") {sh '.build/scripts/test_unit.sh -b $ORO_BASELINE_VERSION'}
                            }
                        }
                        stage('Build:test:image') {
                            steps {
                                dir("$WORKSPACE/../$BUILD_TAG") {
                                    sh '''
                                        docker buildx build --pull --load --rm --build-arg ORO_BASELINE_VERSION -t ${ORO_IMAGE_TEST,,}:$ORO_IMAGE_TAG -f ".build/docker/Dockerfile-test" .
                                    '''
                                }
                            }
                        }
                        stage('Build:test:install') {
                            steps {
                                dir("$WORKSPACE/../$BUILD_TAG") {
                                    sh '''
                                        echo "ORO_ENV=test" >> .build/docker-compose/.env
                                        docker compose -p test_${EXECUTOR_NUMBER} --project-directory .build/docker-compose -f .build/docker-compose/compose-orocommerce-application.yaml down -v
                                        docker compose -p test_${EXECUTOR_NUMBER} --project-directory .build/docker-compose -f .build/docker-compose/compose-orocommerce-application.yaml up --quiet-pull --exit-code-from install-test install-test
                                        rm -rf .build/docker/public_storage
                                        rm -rf .build/docker/private_storage
                                        docker cp test_${EXECUTOR_NUMBER}-install-test-1:/var/www/oro/public/media/ .build/docker/public_storage
                                        docker cp test_${EXECUTOR_NUMBER}-install-test-1:/var/www/oro/var/data/ .build/docker/private_storage
                                        DB_IP=$(docker inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' test_${EXECUTOR_NUMBER}-db-1) docker compose -p test_${EXECUTOR_NUMBER} --project-directory .build/docker-compose -f .build/docker-compose/compose-orocommerce-application.yaml build backup-test
                                    '''
                                }
                            }
                        }
                        // stage('Build:test:functional') {
                        //     environment {
                        //         ORO_FUNCTIONAL_ARGS = ' '
                        //     }
                        //     steps {
                        //         dir("$WORKSPACE/../$BUILD_TAG") {
                        //             sh '''
                        //                 docker compose -p test_${EXECUTOR_NUMBER} --project-directory .build/docker-compose -f .build/docker-compose/compose-orocommerce-application.yaml up --quiet-pull --exit-code-from functional functional
                        //             '''
                        //         }
                        //     }
                        // }
                    }
                }
            }
        }
        // stage('Test:Behat') {
        //     environment {
        //         ORO_BEHAT_ARGS = ' '
        //     }
        //     steps {
        //             sh '''
        //                 docker compose -p prod_${EXECUTOR_NUMBER} --project-directory .build/docker-compose -f .build/docker-compose/compose-orocommerce-application.yaml up --quiet-pull --exit-code-from behat behat
        //             '''
        //     }
        // }
        stage('Push') {
        //     environment {
        //         KEY_FILE = credentials('jenkins_oro-product-development_iam_gserviceaccount_com')
        //         configuration = 'oro-product-development'
        //         credentials = "--configuration ${configuration}"
        //     }
            steps {
                    // gcloud config configurations list | grep ^${configuration} -q || gcloud config configurations create ${configuration}
                    // gcloud config configurations activate ${configuration}
                    // gcloud -q ${credentials} auth activate-service-account --key-file "$KEY_FILE" --project ${configuration}
                    // gcloud ${credentials} auth configure-docker
                    // set -x
                sh '''
                    docker image ls ${ORO_IMAGE}*
                '''
                    // docker image push ${ORO_IMAGE,,}:$ORO_IMAGE_TAG
                    // docker image push ${ORO_IMAGE_INIT,,}:$ORO_IMAGE_TAG
                    // docker image push ${ORO_IMAGE_TEST,,}:$ORO_IMAGE_TAG
                    // docker image push ${ORO_IMAGE_INIT_TEST,,}:$ORO_IMAGE_TAG
                    // docker image rm -f ${ORO_IMAGE,,}:$ORO_IMAGE_TAG ||:
                    // docker image rm -f ${ORO_IMAGE_INIT,,}:$ORO_IMAGE_TAG ||:
                    // docker image rm -f ${ORO_IMAGE_TEST,,}:$ORO_IMAGE_TAG ||:
                    // docker image rm -f ${ORO_IMAGE_INIT_TEST,,}:$ORO_IMAGE_TAG ||:
                    // docker image prune -f
            }
        }
    }
    post {
        always {
            sh '''
                rm -rf "logs"
                mkdir -p "logs"
                cp -rfv "$WORKSPACE/../$BUILD_TAG/var/logs/"* "logs"/ ||:
                printenv | grep ^ORO | sort | sed -e 's/=/="/;s/\$/"/' > "logs"/env-config
                docker ps -a -f "name=.*_.*-.*" > logs/docker_ps.txt ||:
                docker ps -a --format '{{.Names}}' -f "name=.*_.*-.*" | xargs -r -I {} bash -c "docker logs {} > logs/docker_logs_{}.txt 2>&1" ||:
                docker ps -a --format '{{.Names}}' -f "name=.*_.*-redis-.*" | xargs -r -I {} bash -c "docker exec -t {} redis-cli info > logs/docker_{}_info.txt 2>&1" ||:
                docker ps -a --format '{{.Names}}' -f "name=.*_.*-functional-.*" | xargs -r -I {} bash -c "docker cp {}:/var/www/oro//var/logs/junit logs" ||:
                docker ps -a --format '{{.Names}}' -f "name=.*_.*-functional-.*" | xargs -r -I {} bash -c "docker cp {}:/var/www/oro//var/logs/functional logs" ||:
                docker ps -a --format '{{.Names}}' -f "name=.*_.*-behat-.*" | xargs -r -I {} bash -c "docker cp {}:/var/www/oro//var/logs/junit logs" ||:
                docker ps -a --format '{{.Names}}' -f "name=.*_.*-behat-.*" | xargs -r -I {} bash -c "docker cp {}:/var/www/oro//var/logs/behat logs" ||:
                docker compose -p prod_${EXECUTOR_NUMBER} --project-directory $WORKSPACE/.build/docker-compose -f $WORKSPACE/.build/docker-compose/compose-orocommerce-application.yaml down -v ||:
                docker compose -p test_${EXECUTOR_NUMBER} --project-directory $WORKSPACE/../$BUILD_TAG/.build/docker-compose -f $WORKSPACE/../$BUILD_TAG/.build/docker-compose/compose-orocommerce-application.yaml down -v ||:
                rm -rf $WORKSPACE/../${BUILD_TAG}* ||:
            '''
            dir("logs") {
                archiveArtifacts defaultExcludes: false, allowEmptyArchive: true, artifacts: '**', excludes: '**/*.sql', caseSensitive: false
                junit allowEmptyResults: true, testResults: "junit/*.xml"
            }
            script {
                def issuesList = []
                discoverReferenceBuild referenceJob: env.JOB_NAME
                // issuesList.add(scanForIssues([blameDisabled: true, forensicsDisabled: true, tool: pmdParser(name: 'PHP MD', pattern: 'logs/**/static_analysis/phpmd*.xml')]))
                issuesList.add(scanForIssues([blameDisabled: true, forensicsDisabled: true, tool: phpCodeSniffer(name: 'PHP Code Sniffer', pattern: 'logs/**/static_analysis/phpcs*.xml')]))
                issuesList.add(scanForIssues([blameDisabled: true, forensicsDisabled: true, tool: checkStyle(name: 'PHP CS Fixer', pattern: 'logs/**/static_analysis/php-cs-fixer*.xml')]))
                publishIssues issues: issuesList, skipPublishingChecks: true
            }
        }
    }
}

