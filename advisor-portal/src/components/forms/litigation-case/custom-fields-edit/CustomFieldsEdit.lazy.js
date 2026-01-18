import React, { lazy, Suspense } from 'react';

const LazyCustomFieldsEdit = lazy(() => import('./CustomFieldsEdit'));

const CustomFieldsEdit = props => (
  <Suspense fallback={null}>
    <LazyCustomFieldsEdit {...props} />
  </Suspense>
);

export default CustomFieldsEdit;
