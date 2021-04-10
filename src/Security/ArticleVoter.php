<?php

namespace App\Security;

use App\Entity\Article;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ArticleVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';

    protected function supports(string $attribute, $subject)
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT])) {
            return false;
        }

        if (!$subject instanceof Article) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        $article = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($article, $user);
            case self::EDIT:
                return $this->canEdit($article, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Article $article, User $user)
    {
        return $article->getPublished();
    }

    private function canEdit(Article $article, User $user)
    {
        return $user === $article->getUser();
    }
}
