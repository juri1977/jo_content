<?php
declare(strict_types = 1);

return [
  \JO\JoContent\Domain\Model\News::class => [
    'tableName' => 'tx_news_domain_model_news',
  ],
  \JO\JoContent\Domain\Model\FileReference::class => [
    'tableName' => 'sys_file_reference'
  ],
  /*
  \JO\JoMuseo\Domain\Model\FileReference::class => [
    'tableName' => 'sys_file_reference'
  ],
  */
];
