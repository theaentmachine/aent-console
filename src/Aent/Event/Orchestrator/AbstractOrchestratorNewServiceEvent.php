<?php

namespace TheAentMachine\Aent\Event\Orchestrator;

use TheAentMachine\Aent\Context\BaseOrchestratorContext;
use TheAentMachine\Aent\Context\Context;
use TheAentMachine\Aent\Event\AbstractJsonEvent;
use TheAentMachine\Aent\Payload\Builder\NewImageReplyPayload;
use TheAentMachine\Aent\Payload\CI\CINewImagePayload;
use TheAentMachine\Aent\Payload\CI\CINewImageReplyPayload;
use TheAentMachine\Aenthill\Aenthill;
use TheAentMachine\Service\Exception\ServiceException;
use TheAentMachine\Service\Service;

abstract class AbstractOrchestratorNewServiceEvent extends AbstractJsonEvent
{
    /**
     * @param Service $service
     * @return void
     */
    abstract protected function finalizeService(Service $service): void;

    /**
     * @return string
     */
    protected function getEventName(): string
    {
        return 'NEW_SERVICE';
    }

    /**
     * @return bool
     */
    protected function shouldRegisterEvents(): bool
    {
        return false;
    }

    /**
     * @return void
     */
    protected function beforeExecute(): void
    {
        /** @var Context $context */
        $context = Context::fromMetadata();
        $this->output->writeln(sprintf(
            "\n👋 Hello! I'm the aent <info>%s</info> and I'm going to add a new service for <info>%s</info> environment <info>%s</info>.",
            $this->getAentName(),
            $context->getEnvironmentType(),
            $context->getEnvironmentName()
        ));
    }

    /**
     * @param mixed[] $payload
     * @return mixed[]|null
     * @throws ServiceException
     */
    protected function executeJsonEvent(array $payload): ?array
    {
        $service = Service::parsePayload($payload);
        $service = $this->createDockerFileAndBuild($service);
        $this->finalizeService($service);
        return null;
    }

    /**
     * @return void
     */
    protected function afterExecute(): void
    {
        /** @var Context $context */
        $context = Context::fromMetadata();
        $this->output->writeln(
            sprintf(
                "\nI've successfully added the service on your <info>%s</info> environment <info>%s</info>. See you later!",
                $context->getEnvironmentType(),
                $context->getEnvironmentName()
            )
        );
    }

    /**
     * @param Service $service
     * @return Service
     * @throws ServiceException
     */
    private function createDockerFileAndBuild(Service $service): Service
    {
        /** @var Context $context */
        $context = Context::fromMetadata();
        if ($context->isDevelopment() || empty($service->getNeedBuild())) {
            return $service;
        }
        $this->prompt->printAltBlock(sprintf("%s: creating Dockerfile...", $this->getAentName()));
        $response = Aenthill::runJson(BaseOrchestratorContext::BUIlDER_DEPENDENCY_KEY, 'NEW_IMAGE', $service->jsonSerialize());
        $assoc = \GuzzleHttp\json_decode($response[0], true);
        $replyPayload = NewImageReplyPayload::fromArray($assoc);
        return $this->addBuildJobInCI($service, $replyPayload->getDockerfileName());
    }

    /**
     * @param Service $service
     * @param string $dockerfileName
     * @return Service
     */
    private function addBuildJobInCI(Service $service, string $dockerfileName): Service
    {
        $this->prompt->printAltBlock(sprintf("%s: adding build job in CI/CD...", $this->getAentName()));
        $payload = new CINewImagePayload($service->getServiceName(), $dockerfileName);
        $response = Aenthill::runJson(BaseOrchestratorContext::CI_DEPENDENCY_KEY, 'BUILD_JOB', $payload->toArray());
        $assoc = \GuzzleHttp\json_decode($response[0], true);
        $replyPayload = CINewImageReplyPayload::fromArray($assoc);
        $service->setImage($replyPayload->getImageName());
        return $service;
    }
}
