<?php
namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\db\Connection;
use yii\helpers\FileHelper;
use yii\db\Query;
use yii\db\Schema;

/**
 * Copyright (c) 2016-2017 rincha
 * @author rincha
 * @license MIT, For the full copyright and license information, please view the LICENSE
 */

class InstallController extends Controller {

    public function actionIndex() {
        if (!$this->overwrite()) {
            return;
        }
        $res=[];
        $res+=$this->locale();
        $res['locale_language']= str_replace('_', '-', $res['locale_locale']);
        $res+=$this->db();
        $res['cookie_validation_key']=Yii::$app->security->generateRandomString(32);
        $res['salt']=Yii::$app->security->generateRandomString(32);

        $config_files_path=Yii::getAlias('@app').DIRECTORY_SEPARATOR.'config';
        $this->stdout("Create config files:\n");
        foreach ($this->getDistFiles() as $distfile) {
            $content= file_get_contents($distfile);
            foreach ($res as $key=>$value) {
                $content=str_replace('{'.strtoupper($key).'}', $value, $content);
            }
            $b=file_put_contents(
                $config_files_path.DIRECTORY_SEPARATOR.basename($distfile),
                $content
            );
            $this->stdout(basename($distfile).' '.$b.' bytes'."\n");
        }
        $this->stdout("Install config done!\n");
    }

    public function actionDb() {
        $this->stdout("Datebase install migrations\n");
        $dbConf= require(Yii::getAlias('@app/config').DIRECTORY_SEPARATOR.'db.php');
        foreach ($dbConf as $k=>$v) {
            if ($k!=='class') {
                Yii::$app->db->{$k}=$v;
            }
        }
        $componentsConf= require(Yii::getAlias('@app/config').DIRECTORY_SEPARATOR.'components.php');
        foreach ($componentsConf['authManager'] as $k=>$v) {
            if ($k!=='class') {
                Yii::$app->authManager->{$k}=$v;
            }
        }
        $modulesConf= require(Yii::getAlias('@app/config').DIRECTORY_SEPARATOR.'modules.php');
        foreach ($modulesConf as $k=>$v) {
            Yii::$app->setModule($k,$v);
        }

        if (Yii::$app->db->schema->getTableSchema('{{%migration}}') === null) {
            $this->stdout("Datebase: Create migration table\n");
            $tableOptions = null;
            if (Yii::$app->db->driverName === 'mysql') {
                $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
            }
            Yii::$app->db->createCommand()->createTable('{{%migration}}', [
                'version' => Schema::TYPE_STRING . '(180) NOT NULL',
                'apply_time' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
                'PRIMARY KEY (version)'
                    ], $tableOptions)->execute();
        }
        $path = realpath(Yii::$app->basePath . DIRECTORY_SEPARATOR . 'migrations');
        $files = $this->prepareMigrationsFiles(FileHelper::findFiles($path));
        foreach (Yii::$app->modules as $key => $module) {
            if (property_exists(Yii::$app->getModule($key), 'migrations')) {
                $path = realpath(Yii::$app->getModule($key)->basePath . DIRECTORY_SEPARATOR . 'migrations');
                $files+=$this->prepareMigrationsFiles(FileHelper::findFiles($path));
            }
        }
        ksort($files);
        foreach ($files as $file) {
            $this->migrate($file);
        }
        $this->stdout("All migrations applied\n");
    }

    private function overwrite() {
        $exist=$this->getConfigFiles();
        if ($exist) {
            $overwrite=$this->prompt(
                'Overwrite this config files '. implode(', ', $exist).'? (yes/no):',
                [
                    'required' => true,
                    'validator' => function($input, &$error) {
                    if ($input!='yes' && $input!='no') {
                        $error = 'Enter yes or no!';
                        return false;
                    }
                    return true;
                }]
            );
            return $overwrite;
        }
        else {
            return true;
        }
    }

    private function getConfigFiles() {
        $dist=$this->getDistFiles();
        $config_files_path=Yii::getAlias('@app').DIRECTORY_SEPARATOR.'config';
        $config_files=[];
        foreach ($dist as $file) {
            if (file_exists($config_files_path.DIRECTORY_SEPARATOR. basename($file))) {
                $config_files[]=basename($file);
            }
        }
        return $config_files;
    }

    private function getDistFiles() {
        return glob(Yii::getAlias('@app').DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'dist'.DIRECTORY_SEPARATOR.'*.php');
    }

    private function locale() {
        $locale=$this->prompt('Enter application locale (default: '.locale_get_default().'):',[
            'required' => false,
            'validator' => function($input, &$error) {
                if ($input!=='' && !in_array($input, \ResourceBundle::getLocales(''))) {
                    $error = 'Locale not supported, check \ResourceBundle::getLocales("")!';
                    return false;
                }
                return true;
            }]
        );
        if (!$locale) {
            $locale=locale_get_default();
        }
        $timeZone=$this->prompt('Enter time zone (default: '.date_default_timezone_get().'):',[
            'required' => false,
            'validator' => function($input, &$error) {
                if ($input!=='' && !in_array($input, timezone_identifiers_list())) {
                    $error = 'Timezone not supported, check timezone_identifiers_list()!';
                    return false;
                }
                return true;
            }]
        );
        if (!$timeZone) {
            $timeZone=date_default_timezone_get();
        }
        return [
            'locale_locale'=>$locale,
            'locale_timeZone'=>$timeZone,
        ];
    }

    private function db() {
        $dsn=$this->prompt(
            'Enter DSN string for datebase connection (like "mysql:host=localhost;dbname=YOUR_DB_NAME"):',
            ['required' => true,]
        );
        $username=$this->prompt(
            'Enter datebase username:',
            ['required' => true,]
        );
        $password=$this->prompt(
            'Enter datebase password:',
            ['required' => true,]
        );
        $dbc= new Connection([
            'dsn' => $dsn,
            'username' => $username,
            'password' => $password,
        ]);
        $this->stdout('Checking connect to datebase: ');
        $dbc->open();
        $this->stdout("OK\n");
        return [
            'db_dsn'=>$dsn,
            'db_username'=>$username,
            'db_password'=>$password,
        ];
    }

    private function prepareMigrationsFiles($files) {
        $migrations=[];
        foreach ($files as $file) {
            $migrations[basename($file)]=$file;
        }
        return $migrations;
    }

    private function getNamespace($file) {
        $tokens = token_get_all(file_get_contents($file));
	$count = count($tokens);
	$i = 0;
	$namespace = '';
	$namespace_ok = false;
	while ($i < $count) {
            $token = $tokens[$i];
            //var_dump($token); echo "\n(".T_NAMESPACE.")\n";
            if (is_array($token) && $token[0] === T_NAMESPACE) {
                // Found namespace declaration
                while (++$i < $count) {
                    if ($tokens[$i] === ';') {
                        $namespace_ok = true;
                        $namespace = trim($namespace);
                        break;
                    }
                    $namespace .= is_array($tokens[$i]) ? $tokens[$i][1] : $tokens[$i];
                }
                break;
            }
            $i++;
	}
	if (!$namespace_ok) {
		return null;
	} else {
		return $namespace;
	}
    }

    private function migrate($file, $namespace = '\app\migrations\\') {
        $res = false;
        $version = basename($file,'.php');
        $this->stdout('Check migration: ' . $version."\n");
        $row = (new Query())
                ->select('*')
                ->from('{{%migration}}')
                ->where(['version' => $version])
                ->one();
        if (!$row) {
            $this->stdout("Ok\n");
            $migration_class = $this->getNamespace($file).'\\'. $version;
            $this->stdout("Apply migration: " . $migration_class." \n");
            $migration = new $migration_class;
            $migration_result=$migration->up();
            if ($migration_result===true) {
                Yii::$app->db->createCommand()->insert('{{%migration}}', [
                    'version' => $version,
                    'apply_time' => time(),
                ])->execute();
                $res = true;
            }
            elseif ($migration_result===null) {
                $this->stdout("Skip: Migration is not needed.\n");
            }
            else {
                throw new \yii\base\Exception('Error');
            }
        } else {
            $this->stdout("Skip: Migration has already been applied.\n");
        }
        return $res;
    }


}
