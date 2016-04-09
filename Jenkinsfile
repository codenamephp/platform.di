node {
  stage 'Checkout'
  checkout scm
  
  stage 'Composer'
  sh 'composer install'
  
  stage 'Build'
  def antHome = tool '1.9'
  sh "${antHome}/bin/ant"
}
