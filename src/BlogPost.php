<?php


namespace Webdevils\Blog;

use DateTimeImmutable;
use Webdevils\Blog\Exceptions\InvalidBlogPost;
use Webdevils\Blog\Parsers\Parser;
use Webdevils\Blog\Status\Draft;
use Webdevils\Blog\Status\Status;

class BlogPost
{
    use Validation;

    const MIN_TITLE_CHARS = 3;
    const MAX_TITLE_CHARS = 70;
    const MIN_INTRODUCTION_CHARS = 25;
    const MIN_CONTENT_CHARS = 25;

    private Author $author;
    private Category $category;
    private string $title;
    private string $introduction;
    private string $content;

    private Status $status;
    private Parser $parser;

    protected function validate(
        string $title,
        string $introduction,
        string $content
    ): void {
        $errors = [];
        if ($this->isTooShort($title, self::MIN_TITLE_CHARS) || $this->isTooLong($title, self::MAX_TITLE_CHARS)) {
            $errors['title'] = 'Title must be between 3 and 70 characters';
        }
        if ($this->isTooShort($introduction, self::MIN_INTRODUCTION_CHARS)) {
            $errors['introduction'] = 'Introduction must be minimum 25 characters long';
        }
        if ($this->isTooShort($content, self::MIN_CONTENT_CHARS)) {
            $errors['content'] = 'Content must be minimum 25 characters long';
        }
        if (count($errors) > 0) {
            throw new InvalidBlogPost($errors);
        }
    }

    public function __construct(
        Parser $parser,
        Author $author,
        Category $category,
        string $title,
        string $introduction,
        string $content,
    ) {
        $this->validate($title, $introduction, $content);

        $this->author = $author;
        $this->category = $category;
        $this->title = $title;
        $this->introduction = $introduction;
        $this->content = $content;

        $this->status = new Draft();
        $this->parser = $parser;
    }

    public function getAuthor() : Author
    {
        return $this->author;
    }

    public function getCategory() : Category
    {
        return $this->category;
    }

    public function getTitle() : string
    {
        return $this->title;
    }

    public function getIntroduction() : string
    {
        return $this->parser->parse($this->introduction);
    }

    public function getContent() : string
    {
        return $this->parser->parse($this->content);
    }

    public function getStatus() : string
    {
        return $this->status->getName();
    }

    public function getPublishDate() : ?DateTimeImmutable
    {
        return $this->status->getPublishDate();
    }

    public function schedule(DateTimeImmutable $publishDate) : void
    {
        $this->status = $this->status->schedule($publishDate);
    }

    public function publish() : void
    {
        $this->status = $this->status->publish();
    }
}
