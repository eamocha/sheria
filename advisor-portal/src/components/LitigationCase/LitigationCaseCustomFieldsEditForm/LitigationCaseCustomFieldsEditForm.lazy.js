import React, { lazy, Suspense } from 'react';

const LazyLitigationCaseCustomFieldsEditForm = lazy(() => import('./LitigationCaseCustomFieldsEditForm'));

const LitigationCaseCustomFieldsEditForm = props => (
  <Suspense fallback={null}>
    <LazyLitigationCaseCustomFieldsEditForm {...props} />
  </Suspense>
);

export default LitigationCaseCustomFieldsEditForm;
