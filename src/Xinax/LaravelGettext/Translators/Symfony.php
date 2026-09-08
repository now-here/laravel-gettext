<?php namespace Xinax\LaravelGettext\Translators;

use Symfony\Component\Translation\Loader\PoFileLoader;
use Symfony\Component\Translation\Translator as SymfonyTranslator;

use Xinax\LaravelGettext\Config\Models\Config;
use Xinax\LaravelGettext\Adapters\AdapterInterface;
use Xinax\LaravelGettext\FileSystem;

/**
 * Class implemented by Symfony translation component
 * @package Xinax\LaravelGettext\Translators
 */
class Symfony extends BaseTranslator implements TranslatorInterface
{
    /**
     * Symfony translator
     * @var SymfonyTranslator
     */
    protected $symfonyTranslator;

    /**
     * TranslatorInterface constructor.
     *
     * @param Config $config
     * @param AdapterInterface $adapter
     * @param FileSystem $fileSystem
     */
    public function __construct(
        Config $config,
        AdapterInterface $adapter,
        FileSystem $fileSystem
    ) {
        // Sets the package configuration and session handler
        $this->configuration = $config;
        $this->adapter = $adapter;
        $this->fileSystem = $fileSystem;

        // Encoding is set from configuration
        $this->encoding = $this->configuration->getEncoding();
        $this->symfonyTranslator = $this->getTranslator();

    }

    /**
     * Translates a message using the Symfony translation component
     *
     * @param $message
     * @return string
     */
    public function translate($message)
    {
        return $this->symfonyTranslator->trans($message, [], $this->getDomain(), $this->getLocale());
    }

    /**
     * Returns the translator instance
     *
     * @return SymfonyTranslator
     */
    protected function getTranslator()
    {
        return $this->createTranslator();
    }

    /**
     * Set locale overload.
     * Needed to re-build the catalogue when locale changes.
     *
     * @param $locale
     * @return $this
     */
    public function setLocale($locale)
    {
        parent::setLocale($locale);

        $this->symfonyTranslator = $this->createTranslator();
        return $this;
    }

    /**
     * Set domain overload.
     * Needed to re-build the catalogue when domain changes.
     *
     * @param $locale
     * @return $this
     */
    public function setDomain($domain)
    {
        parent::setDomain($domain);

        $this->symfonyTranslator = $this->createTranslator();
        return $this;
    }

    /**
     * Creates a new translator instance
     *
     * @return SymfonyTranslator
     */
    protected function createTranslator()
    {
        $translator = new SymfonyTranslator($this->getLocale());
        $translator->addLoader('po', new PoFileLoader());

        $file = $this->fileSystem->makePOFilePath($this->getLocale(), $this->getDomain());
        $translator->addResource('po', $file, $this->getLocale(), $this->getDomain());
        $translator->getCatalogue($this->getLocale());

        return $translator;
    }

    /**
     * Translates a plural string
     *
     * @param string $singular
     * @param string $plural
     * @param int $amount
     * @return string
     */
    public function translatePlural($singular, $plural, $amount)
    {
        // Symfony translator looks for 'singular|plural' message id in catalog,
        // and it doesn't exist in gettext .po files, so the proper form is
        // selected here and translated as a plain message.
        return $this->symfonyTranslator->trans(
            $amount > 1 ? $plural : $singular,
            ['%count%' => $amount],
            $this->getDomain(),
            $this->getLocale()
        );
    }
}
