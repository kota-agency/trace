<?php
require __DIR__ . '/../vendor/autoload.php';
require '_credentials.php';

use HelpScout\Api\ApiClientFactory;
use HelpScout\Api\Conversations\Conversation;
use HelpScout\Api\Conversations\ConversationFilters;
use HelpScout\Api\Conversations\ConversationRequest;
use HelpScout\Api\Conversations\CustomField;
use HelpScout\Api\Conversations\Threads\ChatThread;
use HelpScout\Api\Conversations\Threads\PhoneThread;
use HelpScout\Api\Tags\Tag;
use HelpScout\Api\Customers\Customer;
use HelpScout\Api\Entity\Collection;

$client = ApiClientFactory::createClient();
$client = $client->useClientCredentials($appId, $appSecret);

// GET conversation
$conversation = $client->conversations()->get(12);

// GET conversation with the threads
$conversationRequest = new ConversationRequest();
$conversationRequest = $conversationRequest->withThreads();
$conversation = $client->conversations()->get(12, $conversationRequest);

// List conversations
$conversations = $client->conversations()
    ->list()
    ->getFirstPage()
    ->toArray();

$filters = (new ConversationFilters())
    ->withMailbox(1)
    ->withFolder(13)
    ->withStatus('all')
    ->withTag('testing')
    ->withAssignedTo(1771)
    ->withNumber(42)
    ->withSortField('createdAt')
    ->withSortOrder('asc')
    // See https://developer.helpscout.com/mailbox-api/endpoints/conversations/list/#query for details on what you can do with query
    ->withQuery('email:"john@appleseed.com"')
    ->withCustomFieldById(123, 'blue');

$conversations = $client->conversations()->list($filters);

/**
 * Create Conversation: Chat thread, initiated by the Customer
 */
$noteCustomer = new Customer();
$noteCustomer->setId(163315601);
$thread = new ChatThread();
$thread->setCustomer($noteCustomer);
$thread->setText('Test');
$conversation = new Conversation();
$conversation->setSubject('Testing the PHP SDK v2: Chat Thread');
$conversation->setStatus('active');
$conversation->setType('chat');
$conversation->setAssignTo(271315);
$conversation->setMailboxId(138367);
$conversation->setCustomer($noteCustomer);
$conversation->setThreads(new Collection([
    $thread,
]));

// Also adding a tag to this conversation
$tag = new Tag();
$tag->setName('testing');
$conversation->addTag($tag);

$conversationId = $client->conversations()->create($conversation);

/**
 * Create Conversation: Phone thread, initiated by a Help Scout user
 */
$user = $client->users()->get(64235);
$noteCustomer = new Customer();
$noteCustomer->setId(193338443); // Customer
$thread = new PhoneThread();
$thread->setCustomer($noteCustomer);
$thread->setCreatedByUser($user);
$thread->setText('Test');

$conversation = new Conversation();
$conversation->setSubject('Testing the PHP SDK v2: Phone Thread');
$conversation->setStatus('active');
$conversation->setType('phone');
$conversation->setMailboxId(80261);
$conversation->setCustomer($noteCustomer);
$conversation->setCreatedByUser($user);
$conversation->setThreads(new Collection([
    $thread,
]));

$conversationId = $client->conversations()->create($conversation);

// Update conversation
$conversationId = 12;
$toMailboxId = 18;
$newCustomerId = 6854;
$assigneeId = 127;
$client->conversations()->move($conversationId, $toMailboxId);
$client->conversations()->updateSubject($conversationId, 'Need more help please');
$client->conversations()->updateCustomer($conversationId, $newCustomerId);
$client->conversations()->publishDraft($conversationId);
$client->conversations()->updateStatus($conversationId, 'closed');
$client->conversations()->assign($conversationId, $assigneeId);
$client->conversations()->unassign($conversationId);
$conversationId = 662118787;

// Update custom fields on a conversation
$customField = new CustomField();
$customField->setId(10524);
$customField->setValue(new DateTime('today'));
$client->conversations()->updateCustomFields($conversationId, [$customField]);

// Update tags on a conversation.  Can either use a tag name or a Tag
$client->conversations()->updateTags($conversationId, [
    'Annual',
    'self-signup-lead'
]);
