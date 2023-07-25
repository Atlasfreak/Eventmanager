<?php
namespace nadar\quill\tests;

class Issue53Test extends DeltaTestCase
{
    public $json = <<<'JSON'
{"ops":[{
    "attributes": {
      "list": {
        "depth": 0,
        "type": "bullet"
      }
    },
    "insert": "\n"
  },
  {
    "insert": "Bullet point content"
  }
]}
JSON;

    public $html = <<<'EOT'
    <ul><li></li></ul><p>Bullet point content</p>
EOT;
}
