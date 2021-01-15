<?php

declare(strict_types=1);

namespace app\Dto;

/**
 * @OA\Schema(
 *      schema="Response",
 * )
 * @OA\Schema(
 *      schema="BadResponse",
 *      allOf={
 *          @OA\Schema(ref="#/components/schemas/Response"),
 *          @OA\Schema(
 *              @OA\Property(
 *                  property="status",
 *                  example="failed",
 *              ),
 *              @OA\Property(property="error_message", example="Error description message"),
 *              @OA\Property(property="error_code", nullable=true, example=400),
 *              @OA\Property(
 *                  property="data",
 *                  example=null
 *              ),
 *          )
 *      }
 * )
 */
final class ApiResponseData
{
    private int $status = 0;
    private ?string $message = null;
    private ?array $data = null;

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(?array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'status' => $this->getStatus(),
            'message' => $this->getMessage(),
            'data' => $this->getData(),
        ];
    }
}