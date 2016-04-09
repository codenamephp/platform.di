
  // Mark the code checkout 'stage'....
  stage 'Checkout'

  // Get some code from a GitHub repository
  checkout scm

  // Mark the code build 'stage'....
  stage 'Build'
    steps {
      shell('composer install')
      ant {
        antInstallation('1.9')
      }
    }
