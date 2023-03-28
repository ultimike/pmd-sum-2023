<?php

namespace Drupal\Tests\drupaleasy_repositories\Kernel;

use Drupal\drupaleasy_repositories\DrupaleasyRepositoriesService;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\drupaleasy_repositories\Traits\RepositoryContentTypeTrait;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;

/**
 * Test description.
 *
 * @group drupaleasy_repositories
 */
class DrupaleasyRepositoriesServiceTest extends KernelTestBase {
  use RepositoryContentTypeTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'drupaleasy_repositories',
    'node',
    'system',
    'field',
    'text',
    'link',
    'user',
  ];

  /**
   * The drupaleasy_repositories service.
   *
   * @var \Drupal\drupaleasy_repositories\DrupaleasyRepositoriesService
   */
  protected DrupaleasyRepositoriesService $drupaleasyRepositoriesService;

  /**
   * The admin user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected UserInterface $adminUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->drupaleasyRepositoriesService = $this->container->get('drupaleasy_repositories.service');
    $this->createRepositoryContentType();

    $this->installEntitySchema('user');
    $this->installEntitySchema('node');

    $aquaman_repo = $this->getAquamanRepo();
    $repo = reset($aquaman_repo);

    $this->adminUser = User::create([
      'name' => $this->randomString(),
    ]);
    $this->adminUser->save();

    //$this->container->get('current_user')->setAccount($this->adminUser);

    $node = Node::create([
      'type' => 'repository',
      'title' => $repo['label'],
      'field_machine_name' => array_key_first($aquaman_repo),
      'field_url' => $repo['url'],
      'field_hash' => '06ec2efe7005ae32f624a9c2d28febd5',
      'field_number_of_issues' => $repo['num_open_issues'],
      'field_source' => $repo['source'],
      'field_description' => $repo['description'],
      'uid' => $this->adminUser->id(),
    ]);
    $node->save();
  }

  /**
   * Test the DrupaleasyRepositoriesService::isUnique method.
   *
   * @covers \Drupal\drupaleasy_repositories\DrupaleasyRepositoriesService::isUnique
   * @dataProvider providerTestIsUnique
   * @test
   */
  public function testIsUnique(bool $expected, array $repo): void {
    $actual = $this->drupaleasyRepositoriesService->isUnique($repo, 999);
    $this->assertEquals($expected, $actual);
  }

  /**
   * Returns sample repository info.
   *
   * @return array
   *   The sample repository info.
   */
  protected function getAquamanRepo() {
    // The order of elements of this array matters when calculating the hash.
    $repo['aquaman-repository'] = [
      'label' => 'The Aquaman repository',
      'description' => 'This is where Aquaman keeps all his crime-fighting code.',
      'num_open_issues' => 6,
      'source' => 'yml',
      'url' => 'http://example.com/aquaman-repo.yml',
    ];
    return $repo;
  }

}
