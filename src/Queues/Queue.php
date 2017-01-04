<?php

/**
 * Zaboy lib (http://zaboy.org/lib/)
 *
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace zaboy\Queues;

use ReputationVIP\QueueClient\QueueClient;
use ReputationVIP\QueueClient\QueueClientInterface;
use ReputationVIP\QueueClient\Adapter\FileAdapter;
use ReputationVIP\QueueClient\PriorityHandler\ThreeLevelPriorityHandler;
use zaboy\Queues\Message;

class Queue
{

    const PUBLIC_DIR = 'www';

    /**
     *
     * @var string
     */
    protected $queueName;

    /**
     *
     * @var QueueClientInterface
     */
    protected $queueClient;

    /**
     *
     * @var int
     */
    protected $delaySeconds;

    public function __construct($queueName, $delaySeconds = 0)
    {
        $this->queueName = $queueName;
        $this->delaySeconds = $delaySeconds;
        $priorityHandler = new ThreeLevelPriorityHandler;
        $adapter = new FileAdapter($this->getPublicDir(), $priorityHandler);
        $this->queueClient = new QueueClient($adapter);
        $queues = $this->queueClient->listQueues();
        if (!in_array($this->queueName, $queues)) {
            $this->queueClient->createQueue($this->queueName);
        }
    }

    public function getMessage($priority = null)
    {
        $messages = $this->queueClient->getMessages($this->queueName, 1, $priority);
        if (isset($messages[0])) {
            $message = new Message($messages[0]);
            $this->queueClient->deleteMessage($this->queueName, $messages[0]);
        } else {
            $message = null;
        }
        return $message;
    }

    public function addMessage($message, $priority = null)
    {
        return $this->queueClient->addMessage($this->queueName, $message, $priority, $this->delaySeconds);
    }

    public function purgeQueue($queueName, $priority = null)
    {
        return $this->queueClient->purgeQueue($queueName, $priority);
    }

    protected function getPublicDir()
    {
        return static::PUBLIC_DIR;
    }

    protected function getQueueDir()
    {
        return $this->getPublicDir() . DIRECTORY_SEPARATOR . 'queues';
    }

}
