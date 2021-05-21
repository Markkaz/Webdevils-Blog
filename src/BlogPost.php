<?php


namespace Webdevils\Blog;

use Webdevils\Blog\Exceptions\InvalidBlogPost;

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
        return $this->introduction;
    }

    public function getContent() : string
    {
        return $this->content;
    }
}
