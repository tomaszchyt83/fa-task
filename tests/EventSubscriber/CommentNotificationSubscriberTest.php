<?php

namespace App\Tests\EventSubscriber;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Event\CommentCreatedEvent;
use App\EventSubscriber\CommentNotificationSubscriber;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CommentNotificationSubscriberTest extends KernelTestCase
{

    use MailerAssertionsTrait;

    private string $sender;

    protected function setUp(): void
    {
        $this->bootKernel();
        $this->sender = 'sender@test.pl';
    }

    public function testGetSubscribedEvents(): void {
        $this->assertEquals([
            CommentCreatedEvent::class => 'onCommentCreated'
        ], CommentNotificationSubscriber::getSubscribedEvents());
    }

    /**
     * @throws \Exception
     */
    public function testOnCommentCreated(): void {
        $author = new User();
        $author->setEmail('author@test.pl');
        $author->setFullName('Unit test author');

        $post = new Post();
        $post->setAuthor($author);
        $post->setTitle('Unit test post title');
        $post->setContent('Test post');
        $post->setSlug('post-title');

        $comment = $this->createMock(Comment::class);
        $comment->method('getPost')->willReturn($post);
        $comment->method('getAuthor')->willReturn($author);
        $comment->method('getContent')->willReturn('Test comment');
        $comment->method('getPublishedAt')->willReturn(new \DateTime());
        $comment->method('getId')->willReturn(1234567);

        $commentNotificationSubscriber = new CommentNotificationSubscriber(
            $this->getContainer()->get(MailerInterface::class),
            $this->getContainer()->get(UrlGeneratorInterface::class),
            $this->getContainer()->get(TranslatorInterface::class),
            $this->sender
        );

        $commentCreatedEvent = new CommentCreatedEvent($comment);

        $commentNotificationSubscriber->onCommentCreated($commentCreatedEvent);

        /** @var Email $lastEmailMessage */
        $lastEmailMessage = $this->getMailerMessage();

        $this->assertEquals(
            'Your post received a comment!',
            $lastEmailMessage->getSubject()
        );
        $this->assertEquals(
            'sender@test.pl',
            $lastEmailMessage->getFrom()[0]->getAddress()
        );
        $this->assertEquals(
            'author@test.pl',
            $lastEmailMessage->getTo()[0]->getAddress()
        );
        $this->assertEquals(
            'Your post "Unit test post title" has received a new comment. You can read the comment by following <a href="http://localhost/en/blog/posts/post-title#comment_1234567">this link</a>',
            $lastEmailMessage->getHtmlBody()
        );
    }
}