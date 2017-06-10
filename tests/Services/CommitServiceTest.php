<?php
/**
 * Created by PhpStorm.
 * User: tgostomski
 * Date: 10.06.17
 * Time: 14:07
 */

namespace TeamNeusta\PushMe\Tests\Services;

use PHPUnit\Framework\TestCase;
use TeamNeusta\PushMe\DTO\Author;
use TeamNeusta\PushMe\DTO\Commit;
use TeamNeusta\PushMe\Services\CommitService;

class CommitServiceTest extends TestCase
{
    /**
     * @test
     */
    public function entryWillBeSkippedIfItIsMergeCommit()
    {
        $commitService = new CommitService();
        $commits = [
            "[message=Merge branch 'bambi/blume'][committed]",
            "[message=weired stuff][committed]"
        ];

        self::assertSame(2, count($commitService->prepareMessages($commits)));
    }

    /**
     * @test
     */
    public function allFieldsWillBeExtractedToDTO()
    {
        $commitService = new CommitService();
        $commits = [
            "[hash=I_AM_THE_HASH][branch=THAT_LIVES_IN_A_BRANCH][message=weired stuff][committed=1496833771][author_name=Jesus Christ][author_mail=jesus.christ@superstar.org]"
        ];

        $commitObject = new Commit();
        $commitObject->setCommitHash('I_AM_THE_HASH')
            ->setBranch('THAT_LIVES_IN_A_BRANCH')
            ->setMessage('weired stuff')
            ->setCommitDate(1496833771)
            ->setAuthor(
                (new Author())
                    ->setName('Jesus Christ')
                    ->setEmail('jesus.christ@superstar.org')
            );


        self::assertSame(1, count($commitService->prepareMessages($commits)));
        self::assertEquals(
            [
                $commitObject
            ],
            $commitService->prepareMessages($commits)
        );
    }
}