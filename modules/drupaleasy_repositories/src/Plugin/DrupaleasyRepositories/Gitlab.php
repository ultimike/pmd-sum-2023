<?php

namespace Drupal\drupaleasy_repositories\Plugin\DrupaleasyRepositories;

use Drupal\drupaleasy_repositories\DrupaleasyRepositories\DrupaleasyRepositoriesPluginBase;
use Gitlab\Client;

/**
 * Plugin implementation of the drupaleasy_repositories.
 *
 * @DrupaleasyRepositories(
 *   id = "gitlab",
 *   label = @Translation("Gitlab"),
 *   description = @Translation("Gitlab.com")
 * )
 */
class Gitlab extends DrupaleasyRepositoriesPluginBase {

  /**
   * Authenticate with Gitlab.
   */
  protected function setAuthentication(): void {
    $this->client = new Client();
    $gitlab_key = $this->keyRepository->getKey('gitlab')->getKeyValues();
    $this->client->authenticate($gitlab_key['personal_access_token'], Client::AUTH_HTTP_TOKEN);
  }

  /**
   * Gets a single repository from Gitlab.
   *
   * @param string $uri
   *   The URI of the repository to get.
   *
   * @return array<string, array<string, string>>
   *   The repositories.
   */
  public function getRepo(string $uri): array {
    // Parse the URI.
    $all_parts = parse_url($uri);
    $parts = explode('/', $all_parts['path']);

    // Set up authentication with the Gitlab API.
    $this->setAuthentication();

    // Option 1: using "search" - but it also returns forks.
    // try {
    //   $projects = $this->client->projects()->all(['search' => $parts[2]]);
    //   if ($projects) {
    //     $repo = [];
    //     foreach ($projects as $project) {
    //       if ($project['web_url'] == $uri) {
    //         $repo = $project;
    //       }
    //     }
    //     if (!$repo) {
    //       return [];
    //     }
    //   }
    //   else {
    //     return [];
    //   }
    // }
    // catch (\Throwable $th) {
    //   return [];
    // }.
    // Option 2: much better than option 1.
    // See https://github.com/GitLabPHP/Client/blob/11.2/src/Api/Projects.php
    try {
      $repo = $this->client->projects()->show($parts[1] . '/' . $parts[2]);
      if (empty($repo)) {
        return [];
      }
    }
    catch (\Throwable $th) {
      $this->messenger->addMessage($this->t('Gitlab error: @error', [
        '@error' => $th->getMessage(),
      ]));
      return [];
    }
    // $repo['open_issues_count'] requires authentication.
    return $this->mapToCommonFormat($repo['path'], $repo['name'], $repo['description'], $repo['open_issues_count'], $repo['web_url']);
  }

  /**
   * {@inheritdoc}
   */
  public function validate($uri): bool {
    $pattern = '/^(https:\/\/)gitlab.com\/[a-zA-Z0-9_-]+\/[a-zA-Z0-9_-]+/';

    if (preg_match($pattern, $uri) == 1) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function validateHelpText(): string {
    return 'https://gitlab.com/vendor/name';
  }

}
