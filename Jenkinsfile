node {
  stage 'Checkout'
  checkout scm
  
  stage 'Composer'
  sh 'composer install'
  
  stage 'Build'
  ant ''
  
  stage "Publish results"
  step([$class: 'WarningsPublisher', canComputeNew: false, canResolveRelativePaths: false, consoleParsers: [
    [parserName: 'PHP Runtime']
    ], defaultEncoding: '', excludePattern: '', healthy: '', includePattern: '', messagesPattern: '', unHealthy: '']
  )
  step([$class: 'CheckStylePublisher', canComputeNew: false, defaultEncoding: '', healthy: '', pattern: 'build/logs/checkstyle.xml', unHealthy: ''])
  step([$class: 'PmdPublisher', canComputeNew: false, defaultEncoding: '', healthy: '', pattern: 'build/logs/pmd.xml', unHealthy: ''])
  step([$class: 'DryPublisher', canComputeNew: false, defaultEncoding: '', healthy: '', pattern: 'build/logs/pmd-cpd.xml', unHealthy: ''])
  
  step(
    [$class: 'XUnitPublisher', testTimeMargin: '3000', thresholdMode: 1, thresholds: [
        [$class: 'FailedThreshold', failureNewThreshold: '0', failureThreshold: '0', unstableNewThreshold: '0', unstableThreshold: '0'], 
        [$class: 'SkippedThreshold', failureNewThreshold: '0', failureThreshold: '0', unstableNewThreshold: '0', unstableThreshold: '0']
      ], 
      tools: [
        [$class: 'PHPUnitJunitHudsonTestType', deleteOutputFiles: true, failIfNotNew: true, pattern: 'build/logs/junit.xml', skipNoTestFiles: false, stopProcessingIfError: true]
      ]
    ]
  )
  step([$class: 'hudson.plugins.violations.ViolationsPublisher'])
  publishHTML(target:[allowMissing: false, alwaysLinkToLastBuild: false, keepAll: false, reportDir: 'build/api', reportFiles: 'index.html', reportName: 'API Documentation'])
}

def ant(args) {
    sh "${tool name: '1.9', type: 'hudson.tasks.Ant$AntInstallation'}/bin/ant ${args}"
}
