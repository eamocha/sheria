import React, { lazy, Suspense } from 'react';

const LazyMenuButton = lazy(() => import('./MenuButton'));

const MenuButton = props => (
  <Suspense fallback={null}>
    <LazyMenuButton {...props} />
  </Suspense>
);

export default MenuButton;
