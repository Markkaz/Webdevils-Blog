<?php


namespace Webdevils\Blog;

class SlugGenerator
{
    private SlugRepository $repository;

    public function __construct(SlugRepository $repository)
    {
        $this->repository = $repository;
    }

    public function generate(string $string) : Slug
    {
        $slugString = str_replace(
            ' ',
            '-',
            trim(
                preg_replace(
                    '/[^a-z\d\-\s]/',
                    '',
                    strtolower($string)
                )
            )
        );
        $slug = new Slug($slugString);

        $sequence = 2;
        while ($this->repository->exists($slug)) {
            $slug = new Slug($slugString . '-' . $sequence);

            $sequence++;
        }

        return $slug;
    }
}
