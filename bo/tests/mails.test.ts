import { describe, expect, it } from 'vitest';
import { moduleId } from '../src/lib/modules/mails';

describe('mails module', () => {
  it('exports its identifier', () => {
    expect(moduleId).toBe('mails');
  });
});
