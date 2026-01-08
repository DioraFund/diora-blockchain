import { motion } from 'framer-motion'
import { ArrowTrendingUpIcon, ArrowTrendingDownIcon } from '@heroicons/react/24/outline'

interface StatsCardProps {
  title: string
  value: string | number
  icon: React.ComponentType<any>
  trend?: string
  href?: string
}

export function StatsCard({ title, value, icon: Icon, trend, href }: StatsCardProps) {
  const isPositive = trend?.startsWith('+')
  const TrendIcon = isPositive ? ArrowTrendingUpIcon : ArrowTrendingDownIcon

  const content = (
    <motion.div
      whileHover={{ scale: 1.02 }}
      className="card hover-lift cursor-pointer group"
      onClick={() => href && window.open(href, '_self')}
    >
      <div className="flex items-start justify-between mb-4">
        <div className="p-3 bg-gradient-to-r from-diora-purple/20 to-diora-blue/20 rounded-xl group-hover:from-diora-purple/30 group-hover:to-diora-blue/30 transition-all">
          <Icon className="w-6 h-6 text-diora-cyan" />
        </div>
        {trend && (
          <div className={`flex items-center space-x-1 text-sm ${isPositive ? 'text-green-500' : 'text-red-500'}`}>
            <TrendIcon className="w-4 h-4" />
            <span>{trend.replace('+', '')}</span>
          </div>
        )}
      </div>
      
      <div>
        <div className="text-2xl font-bold text-dark-text mb-1">{value}</div>
        <div className="text-sm text-dark-text-secondary">{title}</div>
      </div>
    </motion.div>
  )

  return content
}
