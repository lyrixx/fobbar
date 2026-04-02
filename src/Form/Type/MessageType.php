<?php

namespace App\Form\Type;

use App\Entity\Message;
use League\CommonMark\CommonMarkConverter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<Message>
 */
class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class)
        ;

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            $this->convertContentToHtml(...),
            -10,
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }

    public function convertContentToHtml(PostSubmitEvent $event): void
    {
        /** @var Message $message */
        $message = $event->getData();

        $converter = new CommonMarkConverter([
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 10,
        ]);

        $message->contentCompiled = $converter->convert($message->content);
    }
}
