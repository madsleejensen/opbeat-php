<?php namespace spec\Opbeat\Log;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class HttpSpec extends ObjectBehavior
{
    protected $requestAttributes = [
      'DOCUMENT_ROOT' => '/Users/ronni/Desktop',
      'REMOTE_ADDR' => '::1',
      'REMOTE_PORT' => '53385',
      'SERVER_SOFTWARE' => 'PHP 5.5.14 Development Server',
      'SERVER_PROTOCOL' => 'HTTP/1.1',
      'SERVER_NAME' => 'localhost',
      'SERVER_PORT' => '8333',
      'REQUEST_URI' => '/server.php',
      'REQUEST_METHOD' => 'GET',
      'SCRIPT_NAME' => '/server.php',
      'SCRIPT_FILENAME' => '/Users/ronni/Desktop/server.php',
      'PHP_SELF' => '/server.php',
      'HTTP_HOST' => 'localhost:8333',
      'HTTP_CONNECTION' => 'keep-alive',
      'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
      'HTTP_USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.76 Safari/537.36',
      'HTTP_ACCEPT_ENCODING' => 'gzip, deflate, sdch',
      'HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.8,da;q=0.6,nb;q=0.4',
      'HTTP_COOKIE' => 'TJS=5848316a-858b-4c91-a509-ecb90f73bac1; remember_82e5d2c56bdd0811318f0cf078b78bfc=eyJpdiI6IjdZTWlUT0dRQWtkU201WFNUSThlUTErbG9YRlRIeUlqSlVwenZxTDRsRWM9IiwidmFsdWUiOiJmVEpZbWNkTzFpMFk3N090U05qa045Ylo2VjlYV1ZjTkoxU1hOTGFqNFEwPSIsIm1hYyI6IjdlYWVhYjc1ZGRhM2M2OGE3ZGU5ODEwMGEyY2RmZGNmZDgwNzhlZjNkMzkzZmExYjZjZGUwZmNjMjExMDIwYWYifQ%3D%3D; __CT_Data=gpv=692&apv_20660_www07=692; WRUID=0; _ga=GA1.1.746026644.1422282860',
      'REQUEST_TIME_FLOAT' => 1426152582.9758570194244384765625,
      'REQUEST_TIME' => 1426152582,
      'argv' => [],
      'argc' => 0,
    ];

    protected $environmentAttributes = [
      'TERM_PROGRAM' => 'Apple_Terminal',
      'SHELL' => '/bin/bash',
      'TERM' => 'xterm-256color',
      'TMPDIR' => '/var/folders/wk/y4gxhpw11nn_d6tyjsm_3m7h0000gn/T/',
      'Apple_PubSub_Socket_Render' => '/private/tmp/com.apple.launchd.E2KluidsR4/Render',
      'TERM_PROGRAM_VERSION' => '343.6',
      'TERM_SESSION_ID' => '59FC4B25-4F56-4967-A399-8B51721CA743',
      'USER' => 'ronni',
      'SSH_AUTH_SOCK' => '/private/tmp/com.apple.launchd.s8vDZZyQSD/Listeners',
      '__CF_USER_TEXT_ENCODING' => '0x1F5:0x0:0x0',
      'PATH' => '/opt/local/bin:/opt/local/sbin:/Applications/MAMP/bin/php/php5.5.14/bin/:/Users/ronni/.composer/vendor/bin:vendor/bin:/usr/local/bin:/usr/bin:/bin:/usr/sbin:/sbin',
      'PWD' => '/Users/ronni/Desktop',
      'LANG' => 'da_DK.UTF-8',
      'XPC_FLAGS' => '0x0',
      'PS1' => '\\[\\033[0;90m\\]\\A\\[\\033[0m\\]$(git branch &>/dev/null;\\
    if [ $? -eq 0 ]; then \\
      echo "$(echo `git status` | grep "nothing to commit" > /dev/null 2>&1; \\
      if [ "$?" -eq "0" ]; then \\
        # @4 - Clean repository - nothing to commit
        echo "\\[\\033[0;92m\\]"$(__git_ps1 " (%s)"); \\
      else \\
        # @5 - Changes to working tree
        echo "\\[\\033[0;91m\\]"$(__git_ps1 " {%s}"); \\
      fi) \\[\\033[0;93m\\]\\w\\[\\033[0m\\]\\$ "; \\
    else \\
      # @2 - Prompt when not in GIT repo
      echo " \\[\\033[0;93m\\]\\w\\[\\033[0m\\]\\$ "; \\
    fi)',
      'XPC_SERVICE_NAME' => '0',
      'SHLVL' => '1',
      'HOME' => '/Users/ronni',
      'LOGNAME' => 'ronni',
      'SECURITYSESSIONID' => '186a5',
      'OLDPWD' => '/Users/ronni',
      '_' => '/Applications/MAMP/bin/php/php5.5.14/bin/php',
    ];

    function let()
    {
        $this->beConstructedWith($this->requestAttributes, $this->environmentAttributes);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Opbeat\Log\Http');
    }

    function it_serializes_as_null_for_cli_scripts()
    {
        $this->beConstructedWith($_SERVER);
        $this->jsonSerialize()->shouldBeNull();
    }

    function it_returns_correct_data_on_serialization_with_basic_attributes()
    {
        $this->jsonSerialize()->shouldHaveKey('url');
        $this->jsonSerialize()->shouldHaveKey('method');
        $this->jsonSerialize()->shouldHaveKey('cookies');
        $this->jsonSerialize()->shouldHaveKey('headers');
        $this->jsonSerialize()->shouldHaveKey('env');
    }
}
