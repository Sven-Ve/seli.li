<?php

use EasyCorp\Bundle\EasyDeployBundle\Deployer\DefaultDeployer;

return new class extends DefaultDeployer
{
  public function configure()
  {
    return $this->getConfigBuilder()
      // SSH connection string to connect to the remote server (format: user@host-or-IP:port-number)
      ->server('svenvett@svenvett.myhostpoint.ch')
      // the absolute path of the remote server directory where the project is deployed
      ->deployDir('/home/svenvett/www/seli.li')
      // the URL of the Git repository where the project code is hosted
      ->repositoryUrl('git@github.com:Sven-Ve/seli.li.git')
      // the repository branch to deploy
      ->repositoryBranch('main')
      ->remoteComposerBinaryPath('/usr/local/php83/bin/php /home/svenvett/bin/composer')
      ->sharedFilesAndDirs(['.env.local', 'public/.htaccess'])
      ->composerInstallFlags('--prefer-dist --no-interaction --no-dev') // more logging, without quiet
      ->keepReleases(2)
      ->remotePhpBinaryPath('/usr/local/php83/bin/php')
    ;
  }

  public function beforePreparing()
  {
    $this->log('<h3>Copying over the .env files</h3>');
    $this->runRemote('cp {{ deploy_dir }}/repo/.env {{ project_dir }}');
  }

  // run some local or remote commands before optimizing starts
  public function beforeOptimizing()
  {
    $this->log('<h3>Compile asset mapper on prod prod</h3>');
    $this->runRemote('{{ console_bin }} asset-map:compile');
  }

  // run some local or remote commands after the deployment is finished
  public function beforeFinishingDeploy()
  {
    $this->runRemote('killall -9 php-cgi'); // OPCache bei Hostpoint leeren
    $this->runRemote('{{ console_bin }} doctrine:schema:update --force'); // update database
    // $this->runLocal('say "The deployment has finished."');
  }
};
