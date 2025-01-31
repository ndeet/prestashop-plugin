<?php

namespace BTCPay\Form\Type;

use BTCPay\Constants;
use BTCPay\Form\Data\Configuration;
use BTCPayServer\Client\InvoiceCheckoutOptions;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigureType extends TranslatorAwareType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('host', UrlType::class, ['label' => $this->trans('BTCPay Server URL', 'Modules.Btcpay.Admin')])
			->add('api_key', TextType::class, [
				'label'      => $this->trans('BTCPay Server API key', 'Modules.Btcpay.Admin'),
				'attr'       => [
					'placeholder' => empty($this->getConfiguration()->get(Constants::CONFIGURATION_BTCPAY_API_KEY))
						? $this->trans('Leave blank to be redirected to your BTCPay Server for authentication', 'Modules.Btcpay.Admin')
						: null,
					'pattern'     => '[a-zA-Z0-9]+',
				],
				'required'   => false,
				'empty_data' => null,
			])
			->add('speed', ChoiceType::class, [
				'choices'    => [
					$this->trans('Low', 'Modules.Btcpay.Admin')    => InvoiceCheckoutOptions::SPEED_LOW,
					$this->trans('Medium', 'Modules.Btcpay.Admin') => InvoiceCheckoutOptions::SPEED_MEDIUM,
					$this->trans('High', 'Modules.Btcpay.Admin')   => InvoiceCheckoutOptions::SPEED_HIGH,
				],
				'label'      => $this->trans('Transaction speed', 'Modules.Btcpay.Admin'),
				'empty_data' => InvoiceCheckoutOptions::SPEED_MEDIUM,
			])
			->add('order_mode', ChoiceType::class, [
				'choices' => [
					$this->trans('Order before payment', 'Modules.Btcpay.Admin') => Constants::ORDER_MODE_BEFORE,
					$this->trans('Order after payment', 'Modules.Btcpay.Admin')  => Constants::ORDER_MODE_AFTER,
				],
				'label'   => $this->trans('Order creation method', 'Modules.Btcpay.Admin'),
			])
			->add('share_metadata', ChoiceType::class, [
				'choices' => [
					$this->trans('Yes', 'Modules.Btcpay.Admin') => true,
					$this->trans('No', 'Modules.Btcpay.Admin')  => false,
				],
				'label'   => $this->trans('Store customer data within invoice', 'Modules.Btcpay.Admin'),
			]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults(['data_class' => Configuration::class]);
	}

	public function getBlockPrefix(): string
	{
		return 'module_btcpay';
	}
}
