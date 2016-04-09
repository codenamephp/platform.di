node {
  stage 'Checkout'
  checkout scm
  
  stage 'Composer'
  sh 'composer install'
  
  stage 'Build'
  def antHome = tool name: '1.9', type: 'hudson.tasks.Ant$AntInstallation'
  sh "${antHome}/bin/ant"
  
  stage "Publish results"
  step(
    [$class: 'XUnitBuilder', testTimeMargin: '3000', thresholdMode: 1, thresholds: [
        [$class: 'FailedThreshold', failureNewThreshold: '0', failureThreshold: '0', unstableNewThreshold: '0', unstableThreshold: '0'], 
        [$class: 'SkippedThreshold', failureNewThreshold: '0', failureThreshold: '0', unstableNewThreshold: '0', unstableThreshold: '0']
      ], 
      tools: [
        [$class: 'PHPUnitJunitHudsonTestType', deleteOutputFiles: true, failIfNotNew: true, pattern: 'build/logs/junit.xml', skipNoTestFiles: false, stopProcessingIfError: true]
      ]
    ]
  )

}
