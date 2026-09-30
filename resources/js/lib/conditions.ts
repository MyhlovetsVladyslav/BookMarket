export type BookCondition = 'Нова' | 'Ідеальний стан' | 'Як нова' | 'Гарний стан' | 'Задовільний';

export interface ConditionStyle {
    bg: string;
    text: string;
}

export const conditionStyles: Record<string, ConditionStyle> = {
    'Нова': { bg: 'var(--kg-badge-new-bg)', text: 'var(--kg-badge-new-text)' },
    'Ідеальний стан': { bg: 'var(--kg-badge-ideal-bg)', text: 'var(--kg-badge-ideal-text)' },
    'Як нова': { bg: 'var(--kg-badge-ideal-bg)', text: 'var(--kg-badge-ideal-text)' },
    'Гарний стан': { bg: 'var(--kg-badge-good-bg)', text: 'var(--kg-badge-good-text)' },
    'Задовільний': { bg: 'var(--kg-badge-fair-bg)', text: 'var(--kg-badge-fair-text)' },
};

export const conditionOptions: BookCondition[] = [
    'Нова',
    'Ідеальний стан',
    'Гарний стан',
    'Задовільний',
];

export function getConditionStyle(condition: string): ConditionStyle {
    return conditionStyles[condition] ?? { bg: 'var(--kg-surface-alt)', text: 'var(--kg-text-muted)' };
}
