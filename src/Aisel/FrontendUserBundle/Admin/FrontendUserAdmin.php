<?php

/*
 * This file is part of the Aisel package.
 *
 * (c) Ivan Proskuryakov
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Aisel\FrontendUserBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

use Sonata\AdminBundle\Validator\ErrorElement;

/**
 * Frontend users CRUD configuration for Frontend
 *
 * @author Ivan Proskoryakov <volgodark@gmail.com>
 */
class FrontendUserAdmin extends Admin
{
    protected $baseRoutePattern = 'system/user/frontend';
    protected $encoderFactory;

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('aisel.default.information')
            ->add('id', null, array('label' => 'aisel.default.id'))
            ->add('username', null, array('label' => 'aisel.frontenduser.username'))
            ->add('email', null, array('label' => 'aisel.frontenduser.email'))
            ->end()
            ->with('aisel.default.dates')
            ->add('createdAt', null, array('label' => 'aisel.default.created_at'))
            ->add('updatedAt', null, array('label' => 'aisel.default.updated_at'))
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('username')
            ->assertNotBlank()
            ->end()
            ->with('email')
            ->assertNotBlank()
            ->assertNotNull()
            ->assertEmail()
            ->end();
        if (!$object->getId()) {

            $errorElement
                ->with('plainPassword')
                ->assertNotBlank()
                ->end();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('aisel.default.general')
                ->add('id', 'text', array('label' => 'aisel.default.id', 'disabled' => true,
                'required' => false, 'attr' => array('class' => 'form-control')))
                ->add('username', 'text', array('label' => 'aisel.frontenduser.username', 'required' => true, 'attr' => array('class' => 'form-control')))
                ->add('email', 'email', array('label' => 'aisel.frontenduser.email', 'required' => true, 'attr' => array('class' => 'form-control')))
                ->add('plainPassword', 'text', array(
                    'label' => 'aisel.frontenduser.plain_password',
                    'required' => (!$this->getSubject() || is_null($this->getSubject()->getId()))
                ))
            ->add('enabled', null, array('label' => 'aisel.default.enabled','required' => false))
            ->end()
            ->with('aisel.default.dates')
            ->add('createdAt', 'datetime', array(
                'label' => 'aisel.default.created_at',
                'required' => false,
                'disabled' => true, 'attr' => array()))
            ->add('updatedAt', 'datetime', array(
                'label' => 'aisel.default.updated_at',
                'required' => false,
                'attr' => array()))
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper)
    {
        $filterMapper
            ->add('username', null, array('label' => 'aisel.frontenduser.username'))
            ->add('email', null, array('label' => 'aisel.frontenduser.email'));
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('username', null, array('label' => 'aisel.frontenduser.username'))
            ->add('email', null, array('label' => 'aisel.frontenduser.email'))
            ->add('enabled', null, array('label' => 'aisel.default.enabled','editable' => false))
            ->add('updatedAt', 'datetime', array('label' => 'aisel.default.updated_at'))
            ->add('_action', 'actions', array(
                    'actions' => array(
                        'show' => array(),
                        'edit' => array(),
                        'delete' => array(),
                    ))
            );
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($user)
    {
        $encoder = $this->getEncoderFactory()->getEncoder($user);
        $encodedPassword = $encoder->encodePassword($user->getPlainPassword(), $user->getSalt());
        $user->setPassword($encodedPassword);

        $user->setCreatedAt(new \DateTime(date('Y-m-d H:i:s')));
        $user->setUpdatedAt(new \DateTime(date('Y-m-d H:i:s')));
        $user->setLastLogin(new \DateTime(date('Y-m-d H:i:s')));
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($user)
    {
        if ($user->getPlainPassword()) {
            $encoder = $this->getEncoderFactory()->getEncoder($user);
            $encodedPassword = $encoder->encodePassword($user->getPlainPassword(), $user->getSalt());
            $user->setPassword($encodedPassword);
        }

        $user->setUpdatedAt(new \DateTime(date('Y-m-d H:i:s')));
    }

    public function getEncoderFactory()
    {
        return $this->encoderFactory;
    }

    public function setEncoderFactory($encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function toString($object)
    {
        return $object->getId() ? $object->getUsername() : $this->trans('link_add', array(), 'SonataAdminBundle');
    }

}
