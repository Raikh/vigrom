<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiErrorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GetWalletRequest;
use App\Http\Requests\Api\WalletBalanceOperationRequest;
use App\Operations\Wallet\WalletOperation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Psr\Log\LoggerInterface;

class WalletController extends Controller
{
    private LoggerInterface $logger;

    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }
    public function getWallet(
        int $walletId,
        GetWalletRequest $request,
        WalletOperation $walletOperation,
    ): JsonResponse {
        try {
            $walletInfo = $walletOperation->getWalletInfo($walletId);
        } catch (\Throwable $throwable) {
            $this->logger->error('api_get_wallet', [
                'exception' => $throwable,
                'api_get_wallet' => [
                    'wallet_id' => $walletId,
                ],
            ]);

            throw new ApiErrorException(
                'internal_server_error',
                ['message' => $throwable->getMessage()],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return Response::json($walletInfo);
    }

    public function addOperation(
        WalletBalanceOperationRequest $request,
        WalletOperation $walletOperation
    ): JsonResponse {
        try {
            $walletOperation->makeTransaction(
                $request->getWalletId(),
                $request->getCurrencyCode(),
                $request->getAmount(),
                $request->getType(),
                $request->getReason()
            );
        } catch (\Throwable $throwable) {
            $this->logger->error('api_money_transfer_wallet', [
                'exception' => $throwable,
                'api_money_transfer_wallet' => $request,
            ]);

            throw new ApiErrorException(
                'internal_server_error',
                ['message' => $throwable->getMessage()],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return Response::json();
    }
}
