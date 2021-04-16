<?php

namespace app\plugs;


class PlugsConfig
{
    protected $name;
    protected $icon;
    protected $home_view;
    protected $handle_module;
    protected $menu = [];

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param mixed $icon
     */
    public function setIcon($icon): void
    {
        $this->icon = $icon;
    }

    /**
     * @return mixed
     */
    public function getHomeView()
    {
        return $this->home_view;
    }

    /**
     * @param mixed $home_view
     */
    public function setHomeView($home_view): void
    {
        $this->home_view = $home_view;
    }

    /**
     * @return mixed
     */
    public function getHandleModule()
    {
        return $this->handle_module;
    }

    /**
     * @param mixed $handle_module
     */
    public function setHandleModule($handle_module): void
    {
        $this->handle_module = $handle_module;
    }

    /**
     * @return array
     */
    public function getMenu(): array
    {
        return $this->menu;
    }

    /**
     * @param array $menu
     */
    public function setMenu(array $menu): void
    {
        $this->menu = $menu;
    }




}